<?php

    class token 
    {
        private const ab = [
            "A-Z" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "a-z" => "abcdefghijklmnopqrstuvwxyz",
            "0-9" => "0123456789"
        ];

        private $value;
        private const DEFAULT_VALUE = "TOKEN_DEFAULT_VALUE";

        public function __construct(int $len = null, array $alpha_names = array("a-z", "A-Z", "0-9"))
        {
            if ($len !== null && is_int($len))
                self::init($len, $alpha_names);
            else
                $this->value = self::DEFAULT_VALUE;
        }

        public function __toString()
        {
            return self::get();
        }

        // token attr
        public function set($value)
        {
            $this->value = $value;
        }

        // token attr
        public function get()
        { 
            return $this->value; 
        }

        public function init(int $len, array $alpha_names = array("a-z", "A-Z", "0-9"))
        {
            $tkn = self::generate_token($len, $alpha_names);
            self::set($tkn);
        }

        private function generate_token(int $len, array $alpha_names)
        {
            if ($len < 1)
                $len = 10;

            $alphabet = self::get_alphabet($alpha_names);

            $tkn = "";
            
            for ($i=0; $i<$len; $i++)
                $tkn .= $alphabet[$this->crypto_rand_secure(0, strlen($alphabet)-1)];

            return $tkn;
        }

        public function hashed(string $algo = "sha256")
        { 
            return hash($algo, self::get()); 
        }

        private function get_alphabet(array $alpha_names)
        {
            $alphabet = "";
            
            foreach ($alpha_names as $alpha_name)
                $alphabet .= self::ab[$alpha_name];

            return $alphabet;
        }

        private function crypto_rand_secure(int $min, int $max)
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