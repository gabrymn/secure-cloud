<?php

    class token 
    {
        private $value;
        public function val(){ return $this->value; }
        public function hashed($algo = "sha256"){ return hash($algo, $this->value); }

        private CONST ab = [
            "A-Z" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "a-z" => "abcdefghijklmnopqrstuvwxyz",
            "0-9" => "0123456789",
            "1-9" => "123456789"
        ];

        public function __construct($length, $salt = "", $end = "", $alpha_names = array("A-Z","0-9"))
        {
            if ($length === "OTP"){
                $this->value = "";
                for ($i=0; $i<6; $i++)
                {
                    if ($i === 0)
                        $this->value .= self::ab["1-9"][self::crypto_rand_secure(0, strlen(self::ab["1-9"])-1)];
                    else
                        $this->value .= self::ab["0-9"][self::crypto_rand_secure(0, strlen(self::ab["0-9"])-1)];
                }
                return true;
            }

            $alphabet = "";
            foreach ($alpha_names as $alpha_name)
            {
                $alphabet .= isset(self::ab[$alpha_name]) ? self::ab[$alpha_name] : "";
            }
            if ($alphabet === "") $alphabet = self::ab['A-Z'] . self::ab['0-9'];

            if ($length <= (strlen($salt) + strlen($end)))
            {
                return false;
            }   
            
            $max = strlen($alphabet);
            $this->value = "";
            $this->value .= $salt;
            
            for ($i=0; $i<$length - (strlen($salt) + strlen($end)); $i++)
            {
                $this->value .= $alphabet[$this->crypto_rand_secure(0, $max-1)];
            }
            $this->value .= $end;
            return true;
        }

        private function crypto_rand_secure($min, $max)
        {
            $range = $max - $min;
            if ($range < 1) return $min; 
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