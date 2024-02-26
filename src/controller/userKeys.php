<?php

    require_once __DIR__ . '/../model/userSecurity.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';

    class UserKeysController
    {
        public static function getRecoveryKey() : string
        {
            $us = new UserSecurityModel(id_user: $_SESSION['ID_USER']);

            $us->sel_rKeyEnc_by_userID();
            $recoverykey_encrypted = $us->getRecoveryKeyEncrypted();

            $recoverykey = crypto::decrypt($recoverykey_encrypted, $_SESSION['MASTER_KEY']);

            return $recoverykey;
        }

        public static function getCipherKey() : string
        {
            $us = new UserSecurityModel(id_user: $_SESSION['ID_USER']);

            $us->sel_rKeyEnc_by_userID();
            $recoverykey_encrypted = $us->getRecoveryKeyEncrypted();

            $us->sel_cKeyEnc_by_userID();
            $cipherkey_encrypted = $us->getCipherkeyEncrypted();

            $recoverykey = crypto::decrypt($recoverykey_encrypted, $_SESSION['MASTER_KEY']);
            $cipherkey = crypto::decrypt($cipherkey_encrypted, $recoverykey);

            return $cipherkey;
        }
    }

?>