<?php

    class UserSecurity 
    {
        private string $pwd_hash;
        private string $rkey_hash;
        private string $rkey_encrypted;
        private string $ckey_encrypted;
        private string $rkey_iv;
        private string $ckey_iv;
        private int $id_user;

        private const DEFAULT_ID_USER = -1;

        public function init($pwd_hash, $rkey_hash, $rkey_encrypted, $ckey_encrypted, $rkey_iv, $ckey_iv, $id_user = self::DEFAULT_ID_USER){
            
            self::set_pwd_hash($pwd_hash);
            self::set_rkey_hash($rkey_hash);
            self::set_rkey_encrypted($rkey_encrypted);
            self::set_ckey_encrypted($ckey_encrypted);
            self::set_rkey_iv($rkey_iv);
            self::set_ckey_iv($ckey_iv);
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
                self::get_id_user()
            ];
        }


        public static function get_user_sec($id_user, $pwd, $rkey, $rkey_c, $ckey_c, $rkey_iv, $ckey_iv)
        {
            $pwd_hash = password_hash($pwd, PASSWORD_ARGON2ID);
            $rkey_hash = password_hash($rkey, PASSWORD_ARGON2ID);

            $s = new UserSecurity();
            $s->init($pwd_hash, $rkey_hash, $rkey_c, $ckey_c, $rkey_iv, $ckey_iv, $id_user);
            return $s;
        }
    }

?>