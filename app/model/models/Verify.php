<?php

    require_once '../ds/mypdo.php';
    
    class Verify 
    {
        private string $token_hash;
        private int $id_user;
        private $expires;
        private string $email;

        private const EMAIL_DEFAULT_VALUE = "EMAIL_DEFAULT_VALUE";
        private const ID_USER_DEFAULT_VALUE = -1;
        private const EXPIRES_DEFAULT_VALUE = "EXPIRES_DEFAULT_VALUE";
        private const TOKEN_HASH_DEFAULT_VALUE = "TOKEN_HASH_DEFAULT_VALUE";

        private const EXP_MINUTES = 30;
        private const TZ = 'Europe/Rome';
        private const DATE_FORMAT = 'Y-m-d H:i:s';

        public function __construct()
        {
            date_default_timezone_set(self::TZ);
            self::set_datetime(self::EXPIRES_DEFAULT_VALUE);
            self::set_id_user(self::ID_USER_DEFAULT_VALUE);
            self::set_email(self::EMAIL_DEFAULT_VALUE);
            self::set_token(self::TOKEN_HASH_DEFAULT_VALUE);
        }

        public function init($token_hash, $id_user, $email = self::EMAIL_DEFAULT_VALUE)
        {
            self::set_datetime();
            self::set_token($token_hash);
            self::set_id_user($id_user);
            self::set_email($email);
        }

        public function set_email($email)
        {
            $this->email = $email;
        }

        public function get_email()
        {
            return $this->email;
        }

        public function get_token()
        {
            return $this->token_hash;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function get_expires()
        {
            return $this->expires;
        }

        public function set_token($token_hash)
        {
            $this->token_hash = $token_hash;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function set_datetime($expires = false)
        {
            if (!$expires)
                $expires = date(self::DATE_FORMAT, strtotime("+" . strval(self::EXP_MINUTES) . " minutes", time()));
            
            $this->expires = $expires;
        }

        public function check_expires()
        {
            $expires = new DateTime(self::get_expires());
            $now = new DateTime(date(self::DATE_FORMAT));

            return $expires < $now;
        }

        public function get_all()
        {
            $all = array(self::get_token(), self::get_expires(), self::get_id_user());

            if (self::get_email() !== self::EMAIL_DEFAULT_VALUE)
                array_push($all, self::get_email());

            return $all;
        }

        public static function ins_verify(&$conn, $ver, $qrys_dir)
        {
            $qry_file = $qrys_dir . "ins_verify.sql";
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams($ver->get_all(), $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function sel_id_from_tkn(&$mypdo, $tkn, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_id_from_tkn.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($tkn, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
                
                $id_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($id_user === array())
                    return -1;
                else
                    return intval($id_user[0]['id_user']);
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function del_ver_from_tkn(&$conn, $tkn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "del_ver_from_tkn.sql";

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams(array($tkn, $id_user), $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
    }

?>