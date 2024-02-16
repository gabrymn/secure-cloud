<?php

    declare(strict_types=1);

    require __DIR__ . '/../vendor/autoload.php';

    use RobThree\Auth\TwoFactorAuth;

    class MyTFA extends TwoFactorAuth
    {
        private string $secret;
        private string|null $email;
        private const SERVICE_NAME = 'secure-cloud';

        public function __construct(string|null $email = null, string|bool $secret = false)
        {
            parent::__construct();

            if ($secret === false)
                self::set_secret(self::createSecret());
            else
                self::set_secret($secret);

            self::set_email($email);
        }

        public static function get_random_secret() : string
        {
            return (new parent())->createSecret();
        }

        public function set_email($email) : void
        {
            $this->email = $email;
        }

        public function get_email() : string|null
        {   
            return $this->email;
        }

        public function set_secret(string $secret) : void
        {
            $this->secret = $secret;
        }

        public function get_secret() : string
        {
            return $this->secret;
        }
                
        public function get_code() : string
        {
            return self::getCode(self::get_secret());
        }
        
        public function get_qrcode_url() : string
        {
            $label = "";
            $label .= self::SERVICE_NAME;

            if (self::get_email() !== null)
                $label .= (": " . self::get_email());

            return self::getQRCodeImageAsDataUri($label, self::get_secret());
        }

        public function codeIsValid($input_code) : bool
        {
            return self::verifyCode(self::get_secret(), $input_code);
        }
    }
?>