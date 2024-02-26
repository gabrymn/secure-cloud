<?php

    /**
     * Class CryptoRNDString
     * It generates cryptographically-strong-random strings
     */

    class CryptoRNDString extends DataStructure
    {
        private string $alphabet;
        private int $alphabet_len;

        private const MIN_LEN = 10;
        private const MIN_ALPHABET_LEN = 20;

        public function __construct(?string $alphabet = null)
        {
            if ($alphabet === null || strlen($alphabet) < self::MIN_ALPHABET_LEN)
            {
                $this->setAlphabet
                (
                    implode(range('a', 'z')) . implode(range('A', 'Z')) . implode(range(0, 9))
                );
            }
            else
            {
                $this->setAlphabet($alphabet);
            }
        }

        private function setAlphabet(string $alphabet) : void
        {
            $this->alphabet = $alphabet;
            $this->alphabet_len = strlen($alphabet);
        }

        private function getAlphabet() : string
        {
            return $this->alphabet;
        }

        public function generate(int $len) : string
        {
            $unique_string = "";

            $len = $len >= self::MIN_LEN ? $len : self::MIN_LEN;

            for ($i = 0; $i < $len; $i++)
            {
                $random_key = $this->getRandomInteger(0, $this->alphabet_len);
                $unique_string .= $this->alphabet[$random_key];
            }
    
            return $unique_string;
        }
    
        /**
         * @param int $min
         * @param int $max
         * @return int
         */
        private function getRandomInteger(int $min, int $max) : int
        {
            $range = ($max - $min);
    
            if ($range < 0) 
                return $min;
    
            $log = log($range, 2);
    
            // Length in bytes.
            $bytes = (int) ($log / 8) + 1;
    
            // Length in bits.
            $bits = (int) $log + 1;
    
            // Set all lower bits to 1.
            $filter = (int) (1 << $bits) - 1;
    
            do 
            {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
    
                // Discard irrelevant bits.
                $rnd = $rnd & $filter;
    
            } while ($rnd >= $range);
    
            return ($min + $rnd);
        }
    }

?>