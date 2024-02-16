<?php

    require_once 'crypto.php';
    require_once 'two_factor_auth.php';

    class UserKeysHandler
    {
        private string $pwd;
        private string $dkey;
        private string $rkey;
        private string $ckey;
        private string $secret_2fa;
        private string $dkey_salt;

        private const DEFAULT_KEY_VAL = "DEFAULT_KEY_VALUE";
        private const DEFAULT_SALT_VAL = "DEFAULT_SALT_VALUE";

        public function __construct($pwd=null, $dkey=null, $rkey=null, $ckey=null, $secret_2fa=null, $dkey_salt=null)
        {
            self::set_pwd($pwd? $pwd : self::DEFAULT_KEY_VAL);
            self::set_dkey($dkey? $dkey : self::DEFAULT_KEY_VAL);
            self::set_rkey($rkey? $rkey : self::DEFAULT_KEY_VAL);
            self::set_ckey($ckey? $ckey : self::DEFAULT_KEY_VAL);
            self::set_secret_2fa($secret_2fa? $secret_2fa : self::DEFAULT_KEY_VAL);
            self::set_dkey_salt($dkey_salt? $dkey_salt : self::DEFAULT_SALT_VAL);
        }

        public static function get_instance_from_pwd($pwd) : UserKeysHandler|null
        {
            $obj = new UserKeysHandler();

            $dkey_salt = crypto::genSalt();
            $dkey = crypto::deriveKey($pwd, $dkey_salt);
            $rkey = crypto::genAESKey();
            $ckey = crypto::genAESKey();

            $secret_2fa = MyTFA::get_random_secret();

            $obj->set_pwd($pwd);
            $obj->set_dkey($dkey);
            $obj->set_dkey_salt($dkey_salt);
            $obj->set_rkey($rkey);
            $obj->set_ckey($ckey);
            $obj->set_secret_2fa($secret_2fa);

            return $obj;
        }

        public function set_pwd($pwd)
        {
            $this->pwd = $pwd;
        }

        public function get_pwd()
        {
            return $this->pwd;
        }

        public function set_dkey($dkey)
        {
            $this->dkey = $dkey;
        }

        /**
            Derive a key with the password and dkey_salt. 
            If the password or dkey_salt is not provided, 
            they are set to the default values specified in self::DEFAULT_KEY_VAL
        */
        public function set_dkey_auto() : void
        {
            $pwd = self::get_pwd();
            $dkey_salt = self::get_dkey_salt();
            $dkey = crypto::deriveKey($pwd, $dkey_salt);
            self::set_dkey($dkey);
        }

        public function get_dkey()
        {
            return $this->dkey;
        }

        public function set_rkey($rkey)
        {
            $this->rkey = $rkey;
        }

        public function get_rkey()
        {
           return $this->rkey;
        }

        public function set_ckey($ckey)
        {
            $this->ckey = $ckey;
        }

        public function get_ckey()
        {
            return $this->ckey;
        }

        public function set_secret_2fa($secret_2fa)
        {
            $this->secret_2fa = $secret_2fa;
        }

        public function get_secret_2fa()
        {
            return $this->secret_2fa;
        }

        public function set_dkey_salt($dkey_salt)
        {
            $this->dkey_salt = $dkey_salt;
        }

        public function set_dkey_salt_random()
        {
            self::set_dkey_salt(crypto::genSalt());
        }

        public function get_dkey_salt()
        {
            return $this->dkey_salt;
        }

        public function get_pwd_hashed()
        {
            return password_hash($this->pwd, PASSWORD_ARGON2ID);
        }

        public function get_rkey_hashed()
        {
            return password_hash($this->rkey, PASSWORD_ARGON2ID);
        }

        public function get_rkey_encrypted()
        {
            return crypto::encrypt_AES_GCM
            (
                data: self::get_rkey(), 
                key: self::get_dkey(), 
                output_format: BASE64
            );
        }

        public function get_ckey_encrypted()
        {   
            return crypto::encrypt_AES_GCM
            (
                data: self::get_ckey(), 
                key: self::get_rkey(), 
                output_format: BASE64
            );
        }

        public function get_secret_2fa_encrypted()
        {
            return crypto::encrypt_AES_GCM
            (
                data: self::get_secret_2fa(), 
                key: self::get_rkey(), 
                output_format: BASE64
            );
        }
    }


?>