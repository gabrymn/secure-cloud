<?php

    require_once 'crypto.php';
    require_once 'my_two_factor_auth.php';

    class UserKeysHandler extends DataStructure
    {
        private string $password;
        private string $master_key;
        private string $recovery_key;
        private string $cipher_key;
        private string $secret_2fa;
        private string $master_key_salt;

        public function __construct($password=null, $master_key=null, $recovery_key=null, $cipher_key=null, $secret_2fa=null, $masterkey_salt=null)
        {
            self::setPassword($password? $password : self::DEFAULT_STR);
            self::setMasterKey($master_key? $master_key : self::DEFAULT_STR);
            self::setRecoveryKey($recovery_key? $recovery_key : self::DEFAULT_STR);
            self::setCipherKey($cipher_key? $cipher_key : self::DEFAULT_STR);
            self::setSecret2FA($secret_2fa? $secret_2fa : self::DEFAULT_STR);
            self::setMasterKeySalt($masterkey_salt? $masterkey_salt : self::DEFAULT_STR);
        }

        public static function getInstanceFromPassword(string $password) : UserKeysHandler|null
        {
            $obj = new UserKeysHandler();

            $master_key_salt = crypto::genSalt();
            $master_key = crypto::deriveKey($password, $master_key_salt);
            $recovery_key = crypto::genAESKey();
            $cipher_key = crypto::genAESKey();

            $secret_2fa = MyTFA::getRandomSecret();

            $obj->setPassword($password);
            $obj->setMasterKey($master_key);
            $obj->setMasterKeySalt($master_key_salt);
            $obj->setRecoveryKey($recovery_key);
            $obj->setCipherKey($cipher_key);
            $obj->setSecret2FA($secret_2fa);

            return $obj;
        }

        public function setPassword($password)
        {
            $this->password = $password;
        }

        public function getPassword()
        {
            return $this->password;
        }

        public function setMasterKey($master_key)
        {
            $this->master_key = $master_key;
        }

        /**
            *Derive the master key using the password and master_key_salt provided. 
        */
        public function setMasterKeyAuto() : void
        {
            $password = self::getPassword();
            $master_key_salt = self::getMasterKeySalt();

            if ($password === parent::DEFAULT_STR || $master_key_salt === parent::DEFAULT_STR)

            $master_key = Crypto::deriveKey($password, $master_key_salt);
            self::setMasterKey($master_key);
        }

        public function getMasterKey()
        {
            return $this->master_key;
        }

        public function setRecoveryKey($recovery_key)
        {
            $this->recovery_key = $recovery_key;
        }

        public function getRecoveryKey()
        {
           return $this->recovery_key;
        }

        public function setCipherKey($cipher_key)
        {
            $this->cipher_key = $cipher_key;
        }

        public function getCipherKey()
        {
            return $this->cipher_key;
        }

        public function setSecret2FA($secret_2fa)
        {
            $this->secret_2fa = $secret_2fa;
        }

        public function getsecret2FA()
        {
            return $this->secret_2fa;
        }

        public function setMasterKeySalt($master_key_salt)
        {
            $this->master_key_salt = $master_key_salt;
        }

        public function setMasterKeySaltRandom()
        {
            self::setMasterKeySalt(Crypto::genSalt());
        }

        public function getMasterKeySalt()
        {
            return $this->master_key_salt;
        }

        public function getPasswordHashed($algo = PASSWORD_ARGON2ID)
        {
            return password_hash($this->password, $algo);
        }

        public function getRecoveryKeyHashed($algo = PASSWORD_ARGON2ID)
        {
            return password_hash($this->master_key, $algo);
        }

        public function getRecoveryKeyEncrypted()
        {
            return Crypto::encrypt
            (
                data: self::getRecoveryKey(), 
                key: self::getMasterKey(), 
                output_format: Crypto::BASE64
            );
        }

        public function getCipherKeyEncrypted()
        {   
            return Crypto::encrypt
            (
                data: self::getCipherKey(), 
                key: self::getRecoveryKey(), 
                output_format: crypto::BASE64
            );
        }

        public function getSecret2FA_Encrypted()
        {
            return crypto::encrypt
            (
                data: self::getSecret2FA(), 
                key: self::getRecoveryKey(), 
                output_format: crypto::BASE64
            );
        }
    }


?>