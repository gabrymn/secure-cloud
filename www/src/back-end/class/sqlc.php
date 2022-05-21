<?php

    require_once "response.php";
    require_once "browser.php";
    require_once "dmn.php";

    if (!defined('DMN')) define('DMN', get_dmn()); 

    class sqlc
    {
        private static $conn = null;
        private static $stmt = null;
        const USER_STD_KEY = "zqSxYvjck7e5ORpc9kg0";
        const USER_ADMIN_KEY = "2YGBXYQ8y93dhguc728VXHbk2_h3g782iwkjapzsoj92njl";

        private const QRY =
        [
            "INS_CRED" => "INSERT INTO `secure-cloud`.`users` (email, pass, `name`, surname, joined, 2FA, verified) VALUES (?, ?, ?, ?, CURDATE(), 0, 0)",
            "LOGIN" => "SELECT * FROM `secure-cloud`.`users` WHERE email = ?",
            "ACC_REC" => "INSERT INTO `secure-cloud`.`account_recovery` (id_user, htkn, expires) VALUES (?, ?, ADDTIME(NOW(), 1000))",
            "ID_FROM_EMAIL" => "SELECT id FROM `secure-cloud`.`users` WHERE email = ?",
            "EMAIL_FROM_ID" => "SELECT email FROM `secure-cloud`.`users` WHERE id = ?",
            "TKN_ROW" => "SELECT u.email, r.expires FROM `secure-cloud`.`account_recovery` AS r, `secure-cloud`.`users` AS u WHERE u.id = r.id_user AND r.htkn = ? AND r.expires > NOW()",
            "DEL_TKN" => "DELETE FROM `secure-cloud`.`account_recovery` WHERE htkn = ?",
            "CH_PASS" => "UPDATE `secure-cloud`.`users` SET pass = ? WHERE email = ?",
            "CH_PASS_ID" => "UPDATE `secure-cloud`.`users` SET pass = ? WHERE id = ?",
            "REM_DEL" => "DELETE FROM `secure-cloud`.`remember` WHERE htkn = ?",
            "REM_SEL" => "SELECT * FROM `secure-cloud`.`remember` WHERE htkn = ? AND expires > NOW()",
            "DEL_USER_WITH_EMAIL" => "DELETE FROM `secure-cloud`.`users` WHERE email = ?",
            "TSF_FILE" => "INSERT INTO `secure-cloud`.`transfers` (tdate, `type`, id_user, id_session, id_file) VALUES (NOW(), ?, ?, ?, ?)",
            "SET_2FA" => "UPDATE `secure-cloud`.`users` SET 2FA = ? WHERE id = ?",
            "GET_2FA" => "SELECT 2FA FROM `secure-cloud`.`users` WHERE id = ?",
            "DEL_FILE" => "UPDATE `secure-cloud`.`files` SET view = 0 WHERE idf = ?",
            "SEL_REF" => "SELECT ref FROM `secure-cloud`.`files` WHERE idf = ?",
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
            "SEL_SESS_ALL" => "SELECT * FROM `secure-cloud`.`sessions` WHERE id_user = ? ORDER BY session_status DESC, last_time DESC",
            "SEL_SESS_STATUS" => "SELECT session_status FROM `secure-cloud`.`sessions` WHERE id = ?",
            "INS_FILE_DATA" => "INSERT INTO `secure-cloud`.`files` (idf, fname, ref, size, id_user, mime) VALUES (?,?,?,?,?,?)",
            "SEL_FILEIDS" => "SELECT idf FROM `secure-cloud`.`files` WHERE view = 1 AND id_user = ?",
            "SEL_FILE" => "SELECT f.ref AS ref, f.fname AS nam, f.size AS siz, f.mime AS mme, t.tdate AS dat FROM `secure-cloud`.`files` f, `secure-cloud`.`transfers` t WHERE f.idf = t.id_file AND f.idf = ?",
            "TSF_TBL" => "SELECT f.fname AS filename, f.size AS filesize, t.tdate AS transfer_date, s.ip AS ip_address, t.type AS type FROM files f, transfers t, sessions s WHERE t.id_file = f.idf AND t.id_session = s.id AND t.id_user = ? GROUP BY t.id ORDER BY t.tdate DESC",
            "SEL_USER_BY_ID" => "SELECT `name`, surname, email, notes, 2FA, joined FROM  `secure-cloud`.`users` WHERE id = ?",
            "UPD_USER_BY_ID" => "UPDATE `secure-cloud`.`users` SET `name` = ?, surname = ?, email = ?, notes = ? WHERE id = ?",
            "SEL_PASS" => "SELECT pass FROM `secure-cloud`.`users` WHERE id = ?"
        ];

        public static function connect($address = "localhost", $name = "USER_STD", $dbname = "secure-cloud")
        {
            if ($name !== "USER_STD" && $name !== "USER_ADMIN")
            {
                //self::$conn = new mysqli("localhost", "root", "", $dbname);
                response::server_error(400, "Invalid credentials, connection failed");
            }
            else
            {
                $password = $name==="USER_ADMIN"?self::USER_ADMIN_KEY:self::USER_STD_KEY;
                self::$conn = new mysqli($address, $name, $password, $dbname);
            }

            if (self::$conn->connect_error)
            {
                self::$conn = null;
                response::server_error(500, "Connection failed");
            }
            else
            {
                //self::del_expired_rows();
                // connection ok
                return 1;
            }
        }

        private static function prep($qry)
        {
            self::$stmt = null;
            self::$stmt = self::$conn->prepare($qry);
        }

        public static function TEST_CONNECTION($account = "USER_STD")
        {
            self::connect("localhost", $account);
            $state = self::qry_exec("SELECT * FROM `secure-cloud`.`users`", true);
            echo "<pre>";
                print_r($state);
            echo "</pre>";
            exit;
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

        public static function sel_ref($idf)
        {
            self::prep(self::QRY['SEL_REF']);
            self::$stmt->bind_param("s", $idf);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['ref']) ? $row['ref'] : 0;
        }

        public static function del_file($idf)
        {
            self::prep(self::QRY["DEL_FILE"]);
            self::$stmt->bind_param("s", $idf);
            return self::$stmt->execute();
        }


        public static function get_tsf_table($id_user)
        {
            self::prep(self::QRY["TSF_TBL"]);
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

        // (ip, client, os, device, session_status, rem_htkn)
        public static function add_session($id_session, $http_user_agent, $ip, $id_user, $htkn = null)
        {
            $d = get_browser_info($http_user_agent);
            self::prep(self::QRY['INS_SESS']);
            self::$stmt->bind_param("sssssss", $id_session, $ip, $d['browser'], $d['os_platform'], $d['device'], $id_user, $htkn);
            return self::$stmt->execute();
        }

        public static function sel_fileids($id_user)
        {
            self::prep(self::QRY["SEL_FILEIDS"]);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $result = self::$stmt->get_result();
            $row = true;
            while ($row !== NULL)
            {
                $row = $result->fetch_assoc();
                if ($row === NULL) continue;
                $rows[] = $row['idf'];
            }
            return isset($rows) ? $rows : 0;
        }
        
        public static function sel_file($id_file)
        {
            self::prep(self::QRY["SEL_FILE"]);
            self::$stmt->bind_param("s", $id_file);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return $row;
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

        public static function sel_session_status($id_session)
        {   
            self::prep(self::QRY["SEL_SESS_STATUS"]);
            self::$stmt->bind_param("s", $id_session);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            return isset($row['session_status']) ? $row['session_status'] : 0;
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

        public static function ins_tsf_data($type, $id_user, $id_session, $id_file)
        {
            self::prep(self::QRY['TSF_FILE']);
            self::$stmt->bind_param("siss", $type, $id_user, $id_session, $id_file);
            return self::$stmt->execute();
        }

        public static function ins_file_data($id_file, $fname, $ref, $size, $id_user, $mime)
        {
            self::prep(self::QRY['INS_FILE_DATA']);
            self::$stmt->bind_param("sssiis", $id_file, $fname, $ref, $size, $id_user, $mime);
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


        public static function sel_user($id_user)
        {
            self::prep(self::QRY['SEL_USER_BY_ID']);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $row = self::$stmt->get_result()->fetch_assoc();
            $row['joined'] = date("d-m-Y", strtotime($row['joined']));
            return $row;
        }

        public static function upd_user($name, $surname, $email, $notes, $id_user)
        {
            self::prep(self::QRY['UPD_USER_BY_ID']);
            self::$stmt->bind_param("ssssi", $name, $surname, $email, $notes, $id_user);
            return self::$stmt->execute();
        }
 

        public static function insert_cred($email, $hpass, $name, $surname){
            self::prep(self::QRY['INS_CRED']);
            self::$stmt->bind_param("ssss", $email, $hpass, $name, $surname);
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

        public static function pwd_ok($pwd, $id_user){
            self::prep(self::QRY['SEL_PASS']);
            self::$stmt->bind_param("i", $id_user);
            self::$stmt->execute();
            $data = self::$stmt->get_result()->fetch_assoc();
            if ($data === NULL) return 0;
            if (password_verify($pwd, $data['pass'])) return 1;
            else return 0;
        }
        public static function pwd_ch($pwd, $id_user){
            self::prep(self::QRY['CH_PASS_ID']);
            self::$stmt->bind_param("si", $pwd, $id_user);
            return self::$stmt->execute();
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
