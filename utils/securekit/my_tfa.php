<?php

    declare(strict_types=1);

    require __DIR__ . '/../../vendor/autoload.php';

    class MyTFA extends \RobThree\Auth\TwoFactorAuth
    {
        private string $secret;
        private ?string $email;
        private static string $app_name;

        public function __construct(?string $email = null, string|bool $secret = false)
        {
            parent::__construct();

            self::$app_name = $_ENV['APP_NAME'];

            if ($secret === false)
                $this->setSecret(self::createSecret());
            else
                $this->setSecret($secret);

            $this->setEmail($email);
        }

        public static function getRandomSecret() : string
        {
            return (new parent())->createSecret();
        }

        public function setEmail($email) : void
        {
            $this->email = $email;
        }

        public function getEmail() : string|null
        {   
            return $this->email;
        }

        public function setSecret(string $secret) : void
        {
            $this->secret = $secret;
        }

        public function getSecret() : string
        {
            return $this->secret;
        }
                
        public function getCodeX() : string
        {
            return $this->getCode($this->getSecret());
        }
        
        public function getQrcodeURL() : string
        {
            $label = "";
            $label .= self::$app_name;

            if ($this->getEmail() !== null)
                $label .= (": " . $this->getEmail());

            return $this->getQRCodeImageAsDataUri($label, $this->getSecret());
        }

        public function codeIsValid($input_code) : bool
        {
            return $this->verifyCode($this->getSecret(), $input_code);
        }
    }
?>
