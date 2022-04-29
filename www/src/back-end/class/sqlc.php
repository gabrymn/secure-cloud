<?php

    class sqlc 
    {
        private static $conn = null;
        private static $stmt = null;

        private const QRY = 
        [
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

?>