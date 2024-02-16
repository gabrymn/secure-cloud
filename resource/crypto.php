<?php

    define('BASE64', 'BASE64');
    define('HEX', 'HEX');
    define('BIN', 'BIN');

    class crypto
    {
        public static function genAESKey($bit = 256)
        {
            if (!in_array($bit, [128,192,256]))
                return null;

            $length = $bit / 8;
            $key_bin = openssl_random_pseudo_bytes($length);
            $key_base64 = base64_encode($key_bin);
            return $key_base64;
        }

        public static function genSalt($length = 32)
        {   
            $salt_bin = openssl_random_pseudo_bytes($length);
            $salt_base64 = base64_encode($salt_bin);
            return $salt_base64;
        }
        
        public static function deriveKey($password, $salt)
        {
            $hashing_algo = "sha256";
            $iterations = 10000;
            $length = 64;                // 256bit (sha256)
            $binary = false;

            if (ctype_xdigit($salt))
                $salt = hex2bin($salt);
            else
                $salt = base64_decode($salt);

            return hash_pbkdf2($hashing_algo, $password, $salt, $iterations, $length, $binary);
        }

        public static function encrypt_AES_GCM(string $data, string $key, $output_format) 
        {
            if (!in_array($output_format, [BASE64, HEX]))
                return null; //throw new Exception("Invalid output format");    

            $cipher_algo = 'aes-256-gcm';
            $iv_len = 16; //openssl_cipher_iv_length($cipher_algo);

            if (ctype_xdigit($key))
                $key = hex2bin($key);
            else
                $key = base64_decode($key);
            
            if (!in_array(strlen($key)*8, [128,192,256]))
                return null; //throw new Exception("Invalid key length. AES accepts only 128, 192, or 256-bit keys");    
    
            $iv_bin = openssl_random_pseudo_bytes($iv_len);

            $cipher_text_bin = openssl_encrypt
            (
                $data, 
                $cipher_algo, 
                $key, 
                OPENSSL_RAW_DATA, 
                iv: $iv_bin, 
                tag: $tag_bin
            );
            
            if ($cipher_text_bin === false) 
                return null;
            
            $output = null;

            switch($output_format)
            {
                case BASE64:
                {
                    $output = base64_encode($iv_bin . $cipher_text_bin . $tag_bin);
                    break;
                }
                case HEX:
                {
                    $output = bin2hex($iv_bin . $cipher_text_bin . $tag_bin);
                    break;
                }
            }

            return $output;
        }
        
        public static function decrypt_AES_GCM(string $data, string $key) 
        {
            $cipher_algo = 'aes-256-gcm';
            $iv_len = 16; //openssl_cipher_iv_length($cipher_algo);
            $tag_len = 16;

            $input_format = ctype_xdigit($data) ? HEX : BASE64;

            if (ctype_xdigit($key))
                $key = hex2bin($key);
            else
                $key = base64_decode($key);

            if (!in_array(strlen($key)*8, [128,192,256]))
                return null; //throw new Exception("Invalid key length. AES accepts only 128, 192, or 256-bit keys"); 

            $decoded_data = "";
            
            switch ($input_format)
            {
                case HEX:
                {
                    $decoded_data = hex2bin($data);
                    break;
                }

                case BASE64:
                {
                    $decoded_data = base64_decode($data);
                    break;
                }
            }

            $iv_bin = substr($decoded_data, 0, $iv_len);
            $tag_bin = substr($decoded_data, -$tag_len);

            $cipher_text_bin_len = strlen($decoded_data) - $iv_len - $iv_len;
            $cipher_text_bin = substr($decoded_data, $iv_len, $cipher_text_bin_len);

            $plain_text = openssl_decrypt
            (
                $cipher_text_bin, 
                $cipher_algo, 
                $key, 
                OPENSSL_RAW_DATA, 
                iv: $iv_bin, 
                tag: $tag_bin
            );
            
            if ($plain_text === false) 
                return null;
            
            return $plain_text;
        }

        public static function encryptfile_AES_GCM()
        {
            
        }

        public static function decryptfile_AES_GCM()
        {
            
        }
    }



?>