<?php

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
    }

?>