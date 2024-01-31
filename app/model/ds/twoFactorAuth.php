<?php

    declare(strict_types=1);

    require 'packages/vendor/autoload.php';

    use RobThree\Auth\TwoFactorAuth;

    class TFAuth
    {
        private TwoFactorAuth $tfa;
        private string $secret;
        private string $email;
        private const SERVICE_NAME = 'secure-cloud';

        public function __construct(string $email = "email", string | bool $secret = false)
        {
            self::set_tfa(new TwoFactorAuth());

            if ($secret === false)
                self::set_secret(self::get_tfa()->createSecret());
            else
                self::set_secret($secret);

            self::set_email($email);
        }

        public static function random_secret()
        {
            return (new TwoFactorAuth())->createSecret();
        }

        public function set_email($email)
        {
            $this->email = $email;
        }

        public function get_email()
        {   
            return $this->email;
        }

        private function set_tfa(TwoFactorAuth $tfa)
        {
            $this->tfa = $tfa;
        }

        private function get_tfa()
        {
            return $this->tfa;
        }

        public function set_secret(string $secret)
        {
            $this->secret = $secret;
        }

        public function get_secret()
        {
            return $this->secret;
        }
                
        public function get_code()
        {
            return self::get_tfa()->getCode(self::get_secret());
        }
        
        public function get_qrcode_url()
        {
            return self::get_tfa()->getQRCodeImageAsDataUri(self::get_email(), self::get_secret());
        }
    }
?>