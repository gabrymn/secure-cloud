<?php

    require_once "response.php";
    require_once "browser.php";

    class sqlc
    {
        private static $conn = null;
        private static $stmt = null;

        private const QRY =
        [
            "INS_CRED" => "INSERT INTO `secure-cloud`.`users` (email, pass, logged_with, 2FA, verified) VALUES (?, ?, 'EMAIL', 0, 0)",
            "LOGIN" => "SELECT * FROM `secure-cloud`.`users` WHERE email = ? AND logged_with = 'EMAIL'",
            "ACC_REC" => "INSERT INTO `secure-cloud`.`account_recovery` (id_user, htkn, expires) VALUES (?, ?, ADDTIME(NOW(), 1000))",
            "ID_FROM_EMAIL" => "SELECT id FROM `secure-cloud`.`users` WHERE email = ?",
            "EMAIL_FROM_ID" => "SELECT email FROM `secure-cloud`.`users` WHERE id = ?",
            "TKN_ROW" => "SELECT u.email, r.expires FROM `secure-cloud`.`account_recovery` AS r, `secure-cloud`.`users` AS u WHERE u.id = r.id_user AND r.htkn = ? AND r.expires > NOW()",
            "DEL_TKN" => "DELETE FROM `secure-cloud`.`account_recovery` WHERE htkn = ?",
            "CH_PASS" => "UPDATE `secure-cloud`.`users` SET pass = ? WHERE email = ?",
            "REM_DEL" => "DELETE FROM `secure-cloud`.`remember` WHERE htkn = ?",
            "REM_SEL" => "SELECT * FROM `secure-cloud`.`remember` WHERE htkn = ? AND expires > NOW()",
            "OAUTH2_INS" => "INSERT INTO `secure-cloud`.`users` (email, logged_with, 2FA, verified) VALUES (?, 'GOOGLE_OAUTH2')",
            "OAUTH2_SEL" => "SELECT * FROM `secure-cloud`.`users` WHERE email = ?",
            "DEL_USER_WITH_EMAIL" => "DELETE FROM `secure-cloud`.`users` WHERE email = ?",
            "UPL_FILE" => "INSERT INTO `secure-cloud`.`uploads` (id_file, id_user, size, datet) VALUES (?, ?, ?, NOW())",
            "SET_2FA" => "UPDATE `secure-cloud`.`users` SET 2FA = ? WHERE id = ?",
            "GET_2FA" => "SELECT 2FA FROM `secure-cloud`.`users` WHERE id = ?",
            "IS_VER" => "SELECT verified FROM `secure-cloud`.`users` WHERE id = ?",
            "UPD_IS_VER" => "UPDATE `secure-cloud`.`users` SET verified = ? WHERE id = ?",
            "INS_TKN_VER" => "INSERT INTO `secure-cloud`.`account_verify` (id_user, htkn, expires) VALUES (?, ?, ADDTIME(NOW(), 10000))",
            "SEL_TKN_VER" => "SELECT id_user FROM `secure-cloud`.`account_verify` WHERE htkn = ?",
            "DEL_TKN_VER" => "DELETE FROM `secure-cloud`.`account_verify` WHERE id_user = ?",
            "INS_SESS" => "INSERT INTO `secure-cloud`.`sessions` (id, ip, client, os, device, last_time, session_status, id_user, rem_htkn) VALUES (?,?,?,?,?,NOW(),1,?,?)",
            "SEL_SESS" => "SELECT * FROM `secure-cloud`.`sessions` WHERE id = ? AND session_status = 1",
            "SEL_SESS_HTKN" => "SELECT * FROM `secure-cloud`.`sessions` WHERE rem_htkn = ? AND session_status = 1",
            "UPD_SESS" => "UPDATE `secure-cloud`.`sessions` SET last_time = NOW() WHERE id = ? AND session_status = 1",
            "EXP_SESS" => "UPDATE `secure-cloud`.`sessions` SET session_status = 0 WHERE id = ?",
            "SEL_SESS_ALL" => "SELECT * FROM `secure-cloud`.`sessions` WHERE id_user = ? ORDER BY session_status DESC, last_time DESC"
        ];

        public static function connect($address = "localhost", $name = "root", $password = "", $dbname = "secure-cloud")
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
                self::del_expired_rows();
                return 1;
            }
        }

        public static function init_db(){
            self::create_db();
        }

        public static function create_user_administrator($user, $host, $limits = array(0,0,0,0))
        {
            self::connect();
            $qry = "CREATE USER '$user'@'$host' IDENTIFIED VIA mysql_native_password USING '***';GRANT ALL PRIVILEGES ON *.* TO '$user'@'$host' REQUIRE NONE WITH GRANT OPTION MAX_QUERIES_PER_HOUR $limits[0] MAX_CONNECTIONS_PER_HOUR $limits[1] MAX_UPDATES_PER_HOUR $limits[2] MAX_USER_CONNECTIONS $limits[3];";
            $state = self::qry_exec($qry, false);
            return $state;
        }

        public static function create_db($db_name='secure-cloud', $tables = array("env.sql", "remember.sql", "users.sql", "account_recovery.sql"))
        {
            self::connect("localhost", "root", "", "secure-cloud");
            self::qry_exec("CREATE DATABASE IF NOT EXISTS $db_name", false);
            foreach ($tables as $table)
            {
                $db = file_get_contents("http://127.0.0.1/secure-cloud/www/src/back-end/db/{$table}");

                $r = self::qry_exec($db, false);
                if (!$r) response::server_error();
            }
            return 1;
        }

        private static function prep($qry)
        {
            self::$stmt = null;
            self::$stmt = self::$conn->prepare($qry);
        }

        private static function get_REM_INS_query(int $time){
            return "INSERT INTO `secure-cloud`.`remember` (htkn, expires, id_user) VALUES (?, ADDTIME(NOW(), '20 0:0:0'), ?)";
        }

        public static function set_2fa($id_user, $value){
            if ($value !== 0 && $value !== 1) return false;
            self::prep(self::QRY['SET_2FA']);
            self::$stmt->bind_param("ii", $value, $id_user);
            return self::$stmt->execute();
        }

        // (ip, client, os, device, session_status, rem_htkn)
        public static function add_session($id_session, $http_user_agent, $ip, $id_user, $htkn = null)
        {
            $d = get_browser_info($http_user_agent);
            self::prep(self::QRY['INS_SESS']);
            self::$stmt->bind_param("sssssss", $id_session, $ip, $d['browser'], $d['os_platform'], $d['device'], $id_user, $htkn);
            return self::$stmt->execute();
        }

        public static function sel_session($key, $value)
        {   
            if ($key === "SESSION_ID")
                $index = "SEL_SESS";
            else if ($key === "HTKN")
                $index = "SEL_SESS_HTKN";

            self::prep(self::QRY[$index]);
            self::$stmt->bind_param("s", $value);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['id']) ? $row : 0;
        }

        public static function sel_session_all($id_user)
        {
            self::prep(self::QRY["SEL_SESS_ALL"]);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $result = self::$stmt->get_result();
            $row = true;
            while ($row !== NULL)
            {
                $row = $result->fetch_assoc();
                if ($row === NULL) continue;
                $rows[] = $row;
            }
            return isset($rows) ? $rows : 0;
        }

        public static function upd_session($id_session)
        {
            self::prep(self::QRY['UPD_SESS']);
            self::$stmt->bind_param("s", $id_session);
            return self::$stmt->execute();
        }

        public static function expire_session($id_session)
        {
            self::prep(self::QRY['EXP_SESS']);
            self::$stmt->bind_param("s", $id_session);
            return self::$stmt->execute();
        }

        public static function user_verified($id_user){
            self::prep(self::QRY['IS_VER']);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['verified']) ? $row['verified'] : 0;
        }

        public static function ins_tkn_verify($id, $htkn){
            self::prep(self::QRY['INS_TKN_VER']);
            self::$stmt->bind_param("is", $id, $htkn);
            return self::$stmt->execute();
        }

        public static function get_tkn_verify($htkn){
            self::prep(self::QRY['SEL_TKN_VER']);
            self::$stmt->bind_param("s", $htkn);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['id_user']) ? $row['id_user'] : 0; 
        }

        public static function del_tkn_verify($id_user){
            self::prep(self::QRY['DEL_TKN_VER']);
            self::$stmt->bind_param("i", $id_user);
            return self::$stmt->execute();
        }

        public static function upd_verified($id){
            self::prep(self::QRY['UPD_IS_VER']);
            $value = 1;
            self::$stmt->bind_param("ii", $value, $id);
            return self::$stmt->execute();
        }

        public static function get_2fa($id_user){
            self::prep(self::QRY['GET_2FA']);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['2FA']) ? $row['2FA'] : 0;
        }

        // elimina righe tabella '`secure-cloud.remember`_me' e '`secure-cloud.account_recovery`' scadute
        public static function del_expired_rows(){
            $qry = "DELETE FROM `secure-cloud`.`remember` WHERE expires <= NOW()";
            self::qry_exec($qry, false);
            $qry = "DELETE FROM `secure-cloud`.`account_recovery` WHERE expires <= NOW()";
            self::qry_exec($qry, false);
        }

        public static function upl_file($id_file, $id_user, $size){
            self::prep(self::QRY['UPL_FILE']);
            self::$stmt->bind_param("sii", $id_file, $id_user, $size);
            return self::$stmt->execute();
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
            if ($data === NULL) return 0;
            if (password_verify($pass, $data['pass'])) return 1;
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

        // [`secure-cloud.remember`-me-query]: inserisce riga
        public static function rem_ins($htkn, $id_user, int $time){
            $query = self::get_REM_INS_query($time);
            self::prep($query);
            self::$stmt->bind_param("si", $htkn, $id_user);
            return self::$stmt->execute();
        }

        // [`secure-cloud.remember`-me-query]: seleziona riga che ha come hashedtoken ?
        public static function rem_sel($htkn){
            self::prep(self::QRY['REM_SEL']);
            self::$stmt->bind_param("s", $htkn);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['id_user']) ? $row : 0;
        }

        // [`secure-cloud.remember`-me-query]: rimuove riga che mantiene loggato l'utente
        public static function rem_del($htkn){
            self::prep(self::QRY['REM_DEL']);
            self::$stmt->bind_param("s", $htkn);
            return self::$stmt->execute();
        }

        public static function del_user_with_email($email){
            self::prep(self::QRY['DEL_USER_WITH_EMAIL']);
            self::$stmt->bind_param("s", $email);
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

?>
