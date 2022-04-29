<?php

    class sqlc 
    {
        private static $conn = null;
        private static $stmt = null;

        private const QRY = [
            "INS_CRED" => "INSERT INTO PRSN_users (email, pass, logged_with) VALUES (?, ?, 'EMAIL')",
            "LOGIN" => "SELECT * FROM PRSN_users WHERE email = ? AND logged_with = 'EMAIL'",
            "ACC_REC" => "INSERT INTO PRSN_account_recovery (id_user, htkn, expires) VALUES (?, ?, ADDTIME(NOW(), 1000))",
            "ID_FROM_EMAIL" => "SELECT id FROM PRSN_users WHERE email = ?",
            "EMAIL_FROM_ID" => "SELECT email FROM PRSN_users WHERE id = ?",
            "TKN_ROW" => "SELECT u.email, r.expires 
                            FROM PRSN_account_recovery AS r, PRSN_users AS u 
                            WHERE u.id = r.id_user AND r.htkn = ? AND r.expires > NOW()",
            "DEL_TKN" => "DELETE FROM PRSN_account_recovery WHERE htkn = ?",
            "CH_PASS" => "UPDATE PRSN_users SET pass = ? WHERE email = ?",
            "REM_DEL" => "DELETE FROM PRSN_remember WHERE htkn = ?",
            "REM_SEL" => "SELECT * FROM PRSN_remember WHERE htkn = ? AND expires > NOW()",
            "OAUTH2_INS" => "INSERT INTO PRSN_users (email, logged_with) VALUES (?, 'GOOGLE_OAUTH2')",
            "OAUTH2_SEL" => "SELECT * FROM PRSN_users WHERE email = ?",
        ];

        public static function connect($address = "localhost", $name = "mywebs", $password = "", $dbname = "my_mywebs")
        {
            self::$conn = new mysqli($address, $name, $password, $dbname);
            if (self::$conn->connect_error) 
            {
                self::$conn = null;
                response::server_error(500, "Connection failed");
            }
            else
            {
                // Ogni x giorni le righe scadute dalle tabelle indicate devono essere eliminate
                // per evitare di tenere in memoria dati inutili.
                // Per adesso ogni connessione al database esegue la procedura
                self::del_expired_rows();
            }
        }

        private static function prep($qry)
        {
            self::$stmt = null;
            self::$stmt = self::$conn->prepare($qry);
        }

        private static function get_REM_INS_query(int $time){
            return "INSERT INTO PRSN_remember (htkn, expires, id_user) VALUES (?, ADDTIME(NOW(), '20 0:0:0'), ?)";
        }

        // elimina righe tabella 'remember_me' e 'account_recovery' scadute
        public static function del_expired_rows(){
            $qry = "DELETE FROM PRSN_remember WHERE expires <= NOW()";
            self::qry_exec($qry, false);
            $qry = "DELETE FROM PRSN_account_recovery WHERE expires <= NOW()";
            self::qry_exec($qry, false);
        }

        public static function get_email($id_user){
            self::prep(self::QRY['EMAIL_FROM_ID']);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['email']) ? $row['email'] : 0;
        }

        public static function ch_pass($email, $hpass){
            self::prep(self::QRY['CH_PASS']);
            self::$stmt->bind_param("ss", $hpass, $email);
            return self::$stmt->execute();
        }

        public static function ins_OAuth2($email){
            self::prep(self::QRY['OAUTH2_INS']);
            self::$stmt->bind_param("s", $email);
            return self::$stmt->execute();
        }

        public static function sel_OAuth2($email){
            
            self::prep(self::QRY['OAUTH2_SEL']);
            self::$stmt->bind_param("s", $email);
            self::$stmt->execute();
            $data = self::$stmt->get_result()->fetch_assoc();
            
            if (isset($data['id'])){
                if ($data['logged_with'] === "EMAIL"){
                    return -1;
                }else{
                    return 1;
                    // gia' registrato con google oauth OK
                }
            } else {
                return 0;
            }
        }

        public static function insert_cred($email, $hpass){
            self::prep(self::QRY['INS_CRED']);
            self::$stmt->bind_param("ss", $email, $hpass);
            return self::$stmt->execute();
        }

        public static function login($email, $pass){
            self::prep(self::QRY['LOGIN']);
            self::$stmt->bind_param("s", $email);
            self::$stmt->execute();
            $data = self::$stmt->get_result()->fetch_assoc();
            if (password_verify($pass, $data['pass'])) return true;
            else return 0;
        }

        public static function rec_account($htkn, $id_user){
            self::prep(self::QRY['ACC_REC']);
            self::$stmt->bind_param("is", $id_user, $htkn);
            return self::$stmt->execute();
        }

        public static function get_id_user($email){
            self::prep(self::QRY['ID_FROM_EMAIL']);
            self::$stmt->bind_param("s", $email);
            self::$stmt->execute();
            $data = self::$stmt->get_result()->fetch_assoc();
            return isset($data['id']) ? intval($data['id']) : 0; 
        }

        public static function get_tkn_row($htkn){
            self::prep(self::QRY['TKN_ROW']);
            self::$stmt->bind_param("s", $htkn);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['email']) ? $row : 0;
        }

        public static function del_tkn($htkn){
            self::prep(self::QRY['DEL_TKN']);
            self::$stmt->bind_param("s", $htkn);
            return self::$stmt->execute();
        }

        // [remember-me-query]: inserisce riga
        public static function rem_ins($htkn, $id_user, int $time){
            $query = self::get_REM_INS_query($time);
            self::prep($query);
            self::$stmt->bind_param("si", $htkn, $id_user);
            return self::$stmt->execute();
        }

        // [remember-me-query]: seleziona riga che ha come hashedtoken ?
        public static function rem_sel($htkn){
            self::prep(self::QRY['REM_SEL']);
            self::$stmt->bind_param("s", $htkn);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['id_user']) ? $row : 0;
        }

        // [remember-me-query]: rimuove riga che mantiene loggato l'utente
        public static function rem_del($htkn){
            self::prep(self::QRY['REM_DEL']);
            self::$stmt->bind_param("s", $htkn);
            return self::$stmt->execute();
        }

        // funzione query usata solo per gestire acessi tramite OAuth
        public static function qry_exec($qry, $data = true){
            $result = self::$conn->query($qry);
            if ($data === false){
                return $result;
            } else {
                $row = $result->fetch_assoc();
                return $row;
            }
        }
    }

    class response {

        private const CT_JSON = "Content-Type: application/json; charset=utf-8";
        private const CT_TEXT = "Content-Type: text/plain; charset=utf-8";
        private const CT_HTML = "Content-Type: text/html; charset=UTF-8";

        private const ID_SUCCESSFUL = 200;
        private const ID_SERVER_ERROR = 500;
        private const ID_CLIENT_ERROR = 400;
        
        private const HTTP_RESPONSE_STATUS_CODES = array(

            self::ID_SUCCESSFUL => array(
                
                200 => "OK",
                201 => "Created",
                204 => "No Content"
            ),

            self::ID_CLIENT_ERROR => array(

                400 => "Bad Request",
                401 => "Unauthorized",
                403 => "Forbidden",
                404 => "Not Found",
                405 => "Method Not Allowed",
                429 => "Too Many Requests"
            ),

            self::ID_SERVER_ERROR => array(

                500 => "Internal Server Error",
                501 => "Not Implemented"
            )
        );

        public static function client_error(int $status_code = 400, $status_msg = false, array $array = array(), $file = false){

            if (!self::status_code_valid($status_code, self::ID_CLIENT_ERROR)) response::server_error(500);

            http_response_code($status_code);
            $status_msg = self::get_status_msg(self::ID_CLIENT_ERROR, $status_code, $status_msg);

            $json = json_encode(array_merge(array('success' => false,'status_code' => $status_code,'status_message' => $status_msg), $array), JSON_PRETTY_PRINT);

            if ($file !== false) $file = file_get_contents($file);
            
            self::send($json, true, $file);
        }

        public static function server_error(int $status_code = 500, $status_msg = false, array $array = array(), $file = false){

            if (!self::status_code_valid($status_code, self::ID_SERVER_ERROR)) response::server_error(500);

            http_response_code($status_code);
            $status_msg = self::get_status_msg(self::ID_SERVER_ERROR, $status_code, $status_msg);

            $json = json_encode(array_merge(array('success' => false,'status_code' => $status_code,'status_message' => $status_msg), $array), JSON_PRETTY_PRINT);
            
            self::send($json, true, $file);
        }

        public static function successful(int $status_code = 200, $status_msg = false, array $array = array(), $file = false){

            if (!self::status_code_valid($status_code, self::ID_SUCCESSFUL)) response::server_error(500);

            http_response_code($status_code);
            $status_msg = self::get_status_msg(self::ID_SUCCESSFUL, $status_code, $status_msg);

            $json = json_encode(array_merge(array('success' => true,'status_code' => $status_code,'status_message' => $status_msg), $array), JSON_PRETTY_PRINT);

            self::send($json, false, $file);
        }

        public static function ctype($option){

            switch (strtoupper($option)){
                case 'TEXT': default: {   
                    header(self::CT_TEXT);
                    break;
                }
                case 'JSON': {
                    header(self::CT_JSON);
                    break;
                }
                case 'HTML': {
                    header(self::CT_HTML);
                    break;
                }
            }
        }

        private static function status_code_valid(int $status_code, int $id){

            return ($status_code >= $id && $status_code <= $id + 99);
        }

        private static function get_status_msg(int $index, int $status_code, $status_msg){

            return ( 
                $status_msg !== false ? 
                    $status_msg : (@self::HTTP_RESPONSE_STATUS_CODES[$index][$status_code] === null ? 
                        "Status Message Not Available" : self::HTTP_RESPONSE_STATUS_CODES[$index][$status_code])
            );
        }

        public static function send($response, $exit, $file = false){

            if ($file !== false){

                self::ctype('HTML');
                echo $file;
                echo "<h3>('$response')</h3>";
                
            } else {         

                self::ctype('JSON');
                echo $response;
            }
            if ($exit) exit;
        }

        public static function format_type($code, $type){
            self::ctype($type);
            self::code($code);
        }

        public static function code($code){
            http_response_code($code);
        }

        public static function ssend($code, &$e, $value){
            // style send => s.send
            self::code($code);
            $e = $value;
        }

        public static function add_html($html, $code = 200){
            self::ctype('HTML');
            self::code($code);
            echo $html;
        }   
    }

    class token 
    {
        private $value;
        public function val(){ return $this->value; }

        private CONST ab = [
            "A-Z" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "a-z" => "abcdefghijklmnopqrstuvwxyz",
            "0-9" => "0123456789"
        ];

        public function __construct($length, $salt = "", $end = "", $alpha_names = array("A-Z","0-9"))
        {
            $alphabet = "";
            foreach ($alpha_names as $alpha_name)
            {
                $alphabet .= isset(self::ab[$alpha_name]) ? self::ab[$alpha_name] : "";
            }
            if ($alphabet === "") $alphabet = self::ab['A-Z'] . self::ab['0-9'];

            if ($length <= (strlen($salt) + strlen($end)))
            {
                return false;
            }   
            
            $max = strlen($alphabet);
            $this->value = "";
            $this->value .= $salt;

            for ($i=0; $i<$length - (strlen($salt) + strlen($end)); $i++)
            {
                $this->value .= $alphabet[$this->crypto_rand_secure(0, $max-1)];
            }
            $this->value .= $end;
        }

        private function crypto_rand_secure($min, $max)
        {
            $range = $max - $min;
            if ($range < 1) return $min; 
            $log = ceil(log($range, 2));
            $bytes = (int) ($log / 8) + 1;
            $bits = (int) $log + 1; 
            $filter = (int) (1 << $bits) - 1; 
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd &= $filter; 
            } while ($rnd > $range);
            return $min + $rnd;
        }
    }

    class client_google {

        public static function get_object(){

            $client = new Google_Client();
            $client->setClientId(self::get_client_id());
            $client->setClientSecret(self::get_client_secret());
            $client->setRedirectUri(self::get_redirect_uri());
            $client->addScope('profile');
            $client->addScope('email');

            return $client;
        }

        private static function get_client_id(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'CLIENT_ID'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }

        private static function get_client_secret(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'CLIENT_SECRET'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }

        private static function get_redirect_uri(){
            sqlc::connect();
            $qry = "SELECT `value` FROM `PRSN_env` WHERE `key` = 'REDIRECT_URI'";
            $value = sqlc::qry_exec($qry)['value'];
            return $value;
        }
    }
 
    class system 
    {
        // 6000 => 1h
        private const ONE_DAY_SQL = 1440000;
        // 3600 => 1h
        private const ONE_DAY_COOKIE = 86400;

        // default remember for 20 days
        public static function remember($id_user, int $days = 20)
        {
            if (!isset($_COOKIE['ALLOW'])) return false;

            $t_sql = self::ONE_DAY_SQL * $days;
            $t_cookie = self::ONE_DAY_COOKIE * $days;

            sqlc::connect();

            $tkn = new token(36, "", "", array("A-Z", "a-z", "0-9"));
            $state = sqlc::rem_ins(hash("sha256", $tkn->val()), $id_user, $t_sql);
            
            if ($state){
                setcookie('logged', 1, time() + $t_cookie, "/");
                setcookie('rm_tkn', $tkn->val(), time() + $t_cookie, "/");
                return true;
            }
            else return false;
        }

        public static function redirect_priv_area($id_user, $session_data = array()){

            session_start();
            $_SESSION['ID_USER'] = $id_user;

            if (count($session_data) > 0)
                foreach ($session_data as $key => $value)
                    $_SESSION[$key] = $value;

            header("Location: private");
        }
    }


?>