<?php

    declare(strict_types=1);

    require __DIR__ . '/../../vendor/autoload.php';

    use RobThree\Auth\TwoFactorAuth;

    class MyTFA extends TwoFactorAuth
    {
        private string $secret;
        private ?string $email;
        private static string $app_name;

        public function __construct(?string $email = null, string|bool $secret = false)
        {
            parent::__construct();

            self::$app_name = $_ENV['APP_NAME'];

            if ($secret === false)
                $this->set_secret(self::createSecret());
            else
                $this->set_secret($secret);

            $this->set_email($email);
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
            return $this->getCode($this->get_secret());
        }
        
        public function get_qrcode_url() : string
        {
            $label = "";
            $label .= self::$app_name;

            if ($this->get_email() !== null)
                $label .= (": " . $this->get_email());

            return $this->getQRCodeImageAsDataUri($label, $this->get_secret());
        }

        public function codeIsValid($input_code) : bool
        {
            return $this->verifyCode($this->get_secret(), $input_code);
        }
    }
?>
