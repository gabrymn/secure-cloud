<?php

    class Verify 
    {
        private string $token;
        private int $id_user;
        private $expires;

        private const EXP_MINUTES = 30;

        public function init($token, $id_user)
        {
            self::set_datetime();
            self::set_token($token);
            self::set_id_user($id_user);
        }

        public function get_token()
        {
            return $this->token;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function get_expires()
        {
            return $this->expires;
        }

        public function set_token($token)
        {
            $this->token = $token;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function set_datetime($expires = false)
        {
            if (!$expires)
            {
                date_default_timezone_set('Europe/Rome');
                $expires = date('Y-m-d H:i:s', strtotime("+" . strval(self::EXP_MINUTES) . " minutes", time()));
            }
            
            $this->expires = $expires;
        }

        public function check_expires()
        {
            date_default_timezone_set('Europe/Rome');
            $expires = new DateTime(self::get_expires());
            $now = new DateTime(date("Y-m-d H:i:s"));

            return $expires < $now;
        }

        public function get_all()
        {
            return
            [
                self::get_token(),
                self::get_expires(),
                self::get_id_user()
            ];
        }
    }

?>