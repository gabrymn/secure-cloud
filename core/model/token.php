<?php

    class token 
    {
        private CONST ab = [
            "A-Z" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "a-z" => "abcdefghijklmnopqrstuvwxyz",
            "0-9" => "0123456789"
        ];

        private $value;

        private function set_token_value($value)
        {
            self::$value = $value;
        }

        public function get()
        { 
            return $this->value; 
        }

        public function __construct($length, $alpha_names = array("a-z","0-9"))
        {
            $alphabet = self::get_alphabet($alpha_names);

            $temp = "";
            
            for ($i=0; $i<$length; $i++)
                $temp .= $alphabet[$this->crypto_rand_secure(0, strlen($alphabet)-1)];

            self::set_token_value($temp);

            return true;
        }

        public function hashed($algo = "sha256")
        { 
            return hash($algo, $this->value); 
        }

        private function get_alphabet($alpha_names)
        {
            $alphabet = "";
            
            foreach ($alpha_names as $alpha_name)
            {
                $alphabet .= isset(self::ab[$alpha_name]) ? self::ab[$alpha_name] : "";
            }

            if ($alphabet === "") 
                $alphabet = self::ab['a-z'] . self::ab['0-9'];

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