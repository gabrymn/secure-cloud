<?php

    declare(strict_types=1);

    require 'vendor/autoload.php';
    
    use Sonata\GoogleAuthenticator\GoogleAuthenticator;
    use Sonata\GoogleAuthenticator\GoogleQrUrl;

    class Google2FA
    {
        private GoogleAuthenticator $g;
        private string $secret;
        private string $email;
        private const SERVICE_NAME = 'secure-cloud';

        public function __construct(string $email = "email", string | bool $secret = false)
        {
            self::set_g(new GoogleAuthenticator());

            if ($secret === false)
                self::set_secret(self::get_g()->generateSecret());
            else
                self::set_secret($secret);

            self::set_email($email);
        }

        public static function gen_rnd_secret()
        {
            $g = new GoogleAuthenticator();
            return $g->generateSecret();
        }

        public function set_email($email)
        {
            $this->email = $email;
        }

        public function get_email()
        {   
            return $this->email;
        }

        private function set_g(GoogleAuthenticator $g)
        {
            $this->g = $g;
        }

        private function get_g()
        {
            return $this->g;
        }

        public function set_secret(string $secret)
        {
            $this->secret = $secret;
        }

        public function get_secret()
        {
            return $this->secret;
        }

        public function get_qrcode_url()
        {
            return GoogleQrUrl::generate(self::get_email(), self::get_secret(), self::SERVICE_NAME);
        }

        public function get_qrcode_img($base64 = true)
        {
            if ($base64)
                return base64_encode(file_get_contents(self::get_qrcode_url()));
            else
                return file_get_contents(self::get_qrcode_url());
        }

        public function get_code()
        {
            return $this->g->getCode(self::get_secret());
        }
    }
?>