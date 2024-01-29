<?php

    require_once '../ds/mypdo.php';

    class UserSecurity 
    {
        private string $pwd_hash;
        private string $rkey_hash;
        private string $rkey_encrypted;
        private string $ckey_encrypted;
        private string $rkey_iv;
        private string $ckey_iv;
        private string $secret_2fa;
        private int $id_user;

        private const DEFAULT_ID_USER = -1;

        public function init($pwd_hash, $rkey_hash, $rkey_encrypted, $ckey_encrypted, $rkey_iv, $ckey_iv, $secret_2fa, $id_user = self::DEFAULT_ID_USER){
            
            self::set_pwd_hash($pwd_hash);
            self::set_rkey_hash($rkey_hash);
            self::set_rkey_encrypted($rkey_encrypted);
            self::set_ckey_encrypted($ckey_encrypted);
            self::set_rkey_iv($rkey_iv);
            self::set_ckey_iv($ckey_iv);
            self::set_secret_2fa($secret_2fa);
            self::set_id_user($id_user);
        }

        public function set_pwd_hash($pwd_hash)
        {
            $this->pwd_hash = $pwd_hash;
        }

        public function get_pwd_hash()
        {
            return $this->pwd_hash;
        }

        public function set_rkey_hash($rkey_hash)
        {
            $this->rkey_hash = $rkey_hash;
        }

        public function get_rkey_hash()
        {
            return $this->rkey_hash;
        }

        public function set_rkey_encrypted($rkey_encrypted)
        {
            $this->rkey_encrypted = $rkey_encrypted;
        }

        public function get_rkey_encrypted()
        {
            return $this->rkey_encrypted;
        }

        public function set_ckey_encrypted($ckey_encrypted)
        {
            $this->ckey_encrypted = $ckey_encrypted;
        }

        public function get_ckey_encrypted()
        {
            return $this->ckey_encrypted;
        }

        public function set_rkey_iv($rkey_iv)
        {
            $this->rkey_iv = $rkey_iv;
        }

        public function get_rkey_iv()
        {
            return $this->rkey_iv;
        }

        public function set_ckey_iv($ckey_iv)
        {
            $this->ckey_iv = $ckey_iv;
        }

        public function get_ckey_iv()
        {
            return $this->ckey_iv;
        }

        public function set_secret_2fa($secret_2fa)
        {
            $this->secret_2fa = $secret_2fa;
        }

        public function get_secret_2fa()
        {
            return $this->secret_2fa;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function get_all()
        {
            return
            [
                self::get_pwd_hash(), 
                self::get_rkey_hash(),
                self::get_rkey_encrypted(),
                self::get_ckey_encrypted(),
                self::get_rkey_iv(),
                self::get_ckey_iv(),
                self::get_secret_2fa(),
                self::get_id_user()
            ];
        }


        public static function get_user_sec($id_user, $pwd, $rkey, $rkey_c, $ckey_c, $rkey_iv, $ckey_iv, $secret_2fa)
        {
            $pwd_hash = password_hash($pwd, PASSWORD_ARGON2ID);
            $rkey_hash = password_hash($rkey, PASSWORD_ARGON2ID);

            $s = new UserSecurity();
            $s->init($pwd_hash, $rkey_hash, $rkey_c, $ckey_c, $rkey_iv, $ckey_iv, $secret_2fa, $id_user);
            return $s;
        }

        // Model UserSec
        public static function qry_ins_user_sec(&$conn, UserSecurity $user_sec, $qrys_dir)
        {
            $qry_file = $qrys_dir . "ins_user_sec.sql";
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams($user_sec->get_all(), $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        // Model userSec
        public static function sel_pwd_from_id(&$conn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_pwd_from_id.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
                
                $pwd = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($pwd === array())
                    return -1;
                else
                    return $pwd[0]['pwd_hash'];
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        // Model UserSec
        public static function sel_secret_2fa_from_id(&$conn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_secret_2fa_from_id.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
                
                $secret_2fa = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($secret_2fa === array())
                    return false;
                else
                    return $secret_2fa[0]['secret_2fa'];
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
    }

?>