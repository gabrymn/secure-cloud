<?php

class AES {

        private const CIPHER = "aes-256-cbc";
        private const IV = "1234567891234567";
        private const KEY = "27839";

        public static function encrypt($data, $compression = 0){
            //$encryption_key = openssl_random_pseudo_bytes(32);
            // Generate an initialization vector
            //$iv_size = openssl_cipher_iv_length($cipher);
            //Data to encrypt
            //$iv = openssl_random_pseudo_bytes($iv_size);
            //$data = $string_img;
            //$encrypted_data = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv);
            if ($compression){
                $data = base64_encode($data);
            }

            $encrypted = openssl_encrypt($data, self::CIPHER, self::KEY, 0, self::IV);
            return $encrypted;
        }

        public static function decrypt($encrypted, $decompression = 0){

            $decrypted = openssl_decrypt($encrypted, self::CIPHER, self::KEY, 0, self::IV);
            if ($decompression){
                $decrypted = base64_decode($decrypted);
            }
            return $decrypted;
        }
    }

?>