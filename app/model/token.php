<?php

    class token 
    {
        private CONST ab = [
            "A-Z" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "a-z" => "abcdefghijklmnopqrstuvwxyz",
            "0-9" => "0123456789"
        ];

        private $alpha_names;
        private $value;

        // token attr
        private function set($value)
        {
            $this->value = $value;
        }

        // token attr
        public function get()
        { 
            return $this->value; 
        }

        private function set_alpha_names($alpha_names)
        {
            $this->alpha_names = array();

            if (is_array($alpha_names) === false)
                $alpha_names = array("a-z", "0-9");

            foreach ($alpha_names as $alpha_name)
                if (array_key_exists($alpha_name, self::ab))
                    array_push($this->alpha_names, $alpha_name);

            if (empty($this->alpha_names))
                $this->alpha_names = array("a-z", "0-9");
        }

        public function get_alpha_names()
        {
            return $this->alpha_names;
        }

        public function __construct($len, $alpha_names = false)
        {
            self::set_alpha_names($alpha_names);
            return self::init($len, $alpha_names);
        }

        private function init($len)
        {
            $tkn_value = self::generate_token($len);
            self::set($tkn_value);
            return true;
        }

        public function refresh($len, $alpha_names = false)
        {
            if ($alpha_names !== false)
                self::set_alpha_names($alpha_names);

            return self::init($len, $alpha_names);
        }

        private function generate_token($len)
        {
            if ($len < 1)
                $len = 10;

            $alphabet = self::get_alphabet();

            $tkn = "";
            
            for ($i=0; $i<$len; $i++)
                $tkn .= $alphabet[$this->crypto_rand_secure(0, strlen($alphabet)-1)];

            return $tkn;
        }

        public function hashed($algo = "sha256")
        { 
            return hash($algo, self::get()); 
        }

        private function get_alphabet()
        {
            $alphabet = "";
            
            foreach ($this->alpha_names as $alpha_name)
                $alphabet .= self::ab[$alpha_name];

            return $alphabet;
        }

        private function crypto_rand_secure($min, $max)
        {
            $range = $max - $min;
            
            if ($range < 1) 
                return $min; 
            
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

?>