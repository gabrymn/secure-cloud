<?php

    require_once __DIR__ . '/../model/user_secrets.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';

    class UserKeysController
    {
        public static function getRecoveryKey() : string
        {
            $us = new UserSecretsModel(id_user: $_SESSION['ID_USER']);

            $us->sel_rKeyEnc_by_userID();
            $recoverykey_encrypted = $us->getRecoveryKeyEncrypted();

            $recoverykey = crypto::decrypt($recoverykey_encrypted, $_SESSION['MASTER_KEY']);

            return $recoverykey;
        }

        public static function getCipherKey() : string
        {
            $us = new UserSecretsModel(id_user: $_SESSION['ID_USER']);

            $us->sel_rKeyEnc_by_userID();
            $recoverykey_encrypted = $us->getRecoveryKeyEncrypted();

            $us->sel_cKeyEnc_by_userID();
            $cipherkey_encrypted = $us->getCipherkeyEncrypted();

            $recoverykey = crypto::decrypt($recoverykey_encrypted, $_SESSION['MASTER_KEY']);
            $cipherkey = crypto::decrypt($cipherkey_encrypted, $recoverykey);

            return $cipherkey;
        }

        public static function getMasterKeyByUserIDSessionToken($id_user, $session_token)
        {
            $us = new UserSecretsModel(id_user: $id_user);
            $ss = new SessionModel(session_token: $session_token);

            $master_key_encrypted = $us->sel_masterKeyEnc_by_userID();
            $session_key_salt = $ss->sel_sessionKeySalt_by_sessionToken();

            $session_key = Crypto::deriveKey($ss->getSessionToken(), $session_key_salt);

            $master_key_plaintext = Crypto::decrypt($master_key_encrypted, $session_key);

            return $master_key_plaintext;
        }
    }

?>