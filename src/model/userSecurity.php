<?php

    require_once __DIR__ . '/model.php';

    class UserSecurityModel extends Model
    {
        private string $password_hash;
        private string $recoverykey_hash;
        private string $recoverykey_encrypted;
        private string $cipherkey_encrypted;
        private string $secret2fa_encrypted;
        private string $masterkey_salt;
        private int $id_user;

        public function __construct($password_hash=null, $recoverykey_hash=null, $recoverykey_encrypted=null, $cipherkey_encrypted=null, $secret2fa_encrypted=null, $masterkey_salt=null, $id_user=null)
        {
            self::setPasswordHash($password_hash ? $password_hash : parent::DEFAULT_STR);
            self::setRecoveryKeyHash($recoverykey_hash ? $recoverykey_hash : parent::DEFAULT_STR);
            self::setRecoveryKeyEncrypted($recoverykey_encrypted ? $recoverykey_encrypted : parent::DEFAULT_STR);
            self::setCipherKeyEncrypted($cipherkey_encrypted ? $cipherkey_encrypted : parent::DEFAULT_STR);
            self::setSecret2faEncrypted($secret2fa_encrypted ? $secret2fa_encrypted: parent::DEFAULT_STR);
            self::setMasterKeySalt($masterkey_salt ? $masterkey_salt: parent::DEFAULT_STR);
            self::setUserID($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function setPasswordHash(string $password_hash) : void
        {
            $this->password_hash = $password_hash;
        }

        public function getPasswordHash() : string
        {
            return $this->password_hash;
        }

        public function setRecoveryKeyHash(string $recoverykey_hash) : void
        {
            $this->recoverykey_hash = $recoverykey_hash;
        }

        public function getRecoveryKeyHash() : string
        {
            return $this->recoverykey_hash;
        }

        public function setRecoveryKeyEncrypted(string $recoverykey_encrypted) : void
        {
            $this->recoverykey_encrypted = $recoverykey_encrypted;
        }

        public function getRecoveryKeyEncrypted() : string
        {
            return $this->recoverykey_encrypted;
        }

        public function setCipherkeyEncrypted(string $cipherkey_encrypted) : void
        {
            $this->cipherkey_encrypted = $cipherkey_encrypted;
        }

        public function getCipherkeyEncrypted() : string
        {
            return $this->cipherkey_encrypted;
        }

        public function setSecret2faEncrypted(string $secret2fa_encrypted) : void
        {
            $this->secret2fa_encrypted = $secret2fa_encrypted;
        }

        public function getSecret2faEncrypted() : string
        {
            return $this->secret2fa_encrypted;
        }

        public function setMasterKeySalt(string $masterkey_salt) : void
        {
            $this->masterkey_salt = $masterkey_salt;
        }

        public function getMasterKeySalt()
        {
            return $this->masterkey_salt;
        }

        public function setUserID(int $id_user) : void
        {
            $this->id_user = $id_user;
        }

        public function getUserID()
        {
            return $this->id_user;
        }

        public function toAssocArray($password_hash=false, $recoverykey_hash=false, $recoverykey_encrypted=false, $cipherkey_encrypted=false, $secret2fa_encrypted=false, $masterkey_salt=false, $id_user=false) : array
        {
            $params = array();
            
            if ($password_hash)
                $params["password_hash"] = $this->getPasswordHash();

            if ($recoverykey_hash)
                $params["recoverykey_hash"] = $this->getRecoveryKeyHash();

            if ($recoverykey_encrypted)
                $params["recoverykey_encrypted"] = $this->getRecoveryKeyEncrypted();

            if ($cipherkey_encrypted)
                $params["cipherkey_encrypted"] = $this->getCipherKeyEncrypted();
            
            if ($secret2fa_encrypted)
                $params["secret2fa_encrypted"] = $this->getSecret2faEncrypted();

            if ($masterkey_salt)
                $params["masterkey_salt"] = $this->getMasterKeySalt();

            if ($id_user)
                $params["id_user"] = $this->getUserID();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO user_security 
            (password_hash, recoverykey_hash, recoverykey_encrypted, cipherkey_encrypted, secret2fa_encrypted, masterkey_salt, id_user)
            VALUES 
            (:password_hash, :recoverykey_hash, :recoverykey_encrypted, :cipherkey_encrypted, :secret2fa_encrypted, :masterkey_salt, :id_user)";
    
            MyPDO::connect('insert');

            return MyPDO::qryExec
            (
                $qry, 
                $this->toAssocArray
                (
                    password_hash:true,
                    recoverykey_hash:true,
                    recoverykey_encrypted:true,
                    cipherkey_encrypted:true,
                    secret2fa_encrypted:true,
                    masterkey_salt:true,
                    id_user:true
                )
            );
        }

        public function sel_pwdHash_by_userID()
        {
            $qry = "SELECT password_hash FROM user_security WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $password_hash = $res[0]['password_hash'];
                $this->setPasswordHash($password_hash);
                return $this->getPasswordHash();
            }
        }

        public function sel_secret2faEnc_by_userID()
        {
            $qry = "SELECT secret2fa_encrypted FROM user_security WHERE id_user = :id_user";
            
            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $secret2fa_encrypted = $res[0]['secret2fa_encrypted'];
                $this->setSecret2faEncrypted($secret2fa_encrypted);
                return $this->getSecret2faEncrypted();
            }
        }
        
        public function sel_rKeyEnc_by_userID()
        {
            $qry = "SELECT recoverykey_encrypted FROM user_security WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $recoverykey_encrypted = $res[0]['recoverykey_encrypted'];
                $this->setRecoveryKeyEncrypted($recoverykey_encrypted);
                return $this->getRecoveryKeyEncrypted();
            }
        }

        public function sel_cKeyEnc_by_userID()
        {
            $qry = "SELECT cipherkey_encrypted FROM user_security WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $cipherkey_encrypted = $res[0]['cipherkey_encrypted'];
                $this->setCipherKeyEncrypted($cipherkey_encrypted);
                return $this->getCipherkeyEncrypted();
            }
        }

        public function sel_mKeySalt_by_userID()
        {
            $qry = "SELECT masterkey_salt FROM user_security WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $masterkey_salt = $res[0]['masterkey_salt'];
                $this->setMasterKeySalt($masterkey_salt);
                return $this->getMasterKeySalt();
            }
        }

        public function sel_rKeyHash_by_userID()
        {
            $qry = 
            "SELECT recoverykey_hash 
            FROM user_security 
            WHERE id_user = :id_user";
            
            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user: true));

            if ($res === false)
                return false;
            else if ($res === array())
                return null;
            else
            {
                $recoverykey_hash = $res[0]['recoverykey_hash'];
                $this->setRecoveryKeyHash($recoverykey_hash);
                return $this->getRecoveryKeyHash();
            }
        }

        public function upd_pwdHash_rKeyEnc_mKeySalt_by_userID()
        {
            $qry = "UPDATE user_security 
            SET password_hash = :password_hash, recoverykey_encrypted = :recoverykey_encrypted, masterkey_salt = :masterkey_salt 
            WHERE id_user = :id_user";

            MyPDO::connect('update');

            return MyPDO::qryExec
            (
                $qry, 
                $this->toAssocArray
                (
                    password_hash:true, 
                    recoverykey_encrypted:true, 
                    masterkey_salt:true, 
                    id_user:true
                )
            );
        }
    }

?>