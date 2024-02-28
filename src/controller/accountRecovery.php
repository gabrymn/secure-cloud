<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/userSecurity.php';
    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/security/userKeysHandler.php';

    class AccountRecoveryController
    {
        public static function renderRecoverPage()
        {
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/recover.php';
        }

        public static function processRecoveryKeyCheck($email, $recovery_key)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                HttpResponse::clientError(400, "Invalid email format");

            $user = new UserModel(email: $email);
            $user->selIDByEmail($user->getEmail());
            
            $us = new UserSecurityModel(id_user: $user->getUserID());
            
            $us->sel_rKeyHash_by_userID();

            if (!password_verify($recovery_key, $us->getRecoveryKeyHash()))
                HttpResponse::clientError(400, "The provided recovery key is incorrect. Please double-check the key and try again.");
            
            session_start();
            
            $_SESSION['RECOVERING_ACCOUNT'] = true;
            $_SESSION['ID_USER'] = $user->getUserID();
            $_SESSION['RECOVERY_KEY'] = $recovery_key;
    
            HttpResponse::successful(200);
        }

        public static function processPasswordReset($password)
        {
            if (strlen($password) < 2)
                HttpResponse::clientError(400, "Invalid password format");

            $ukh = UserKeysHandler::getInstanceFromPassword($password);
            $ukh->setRecoveryKey($_SESSION['RECOVERY_KEY']);

            $us = new UserSecurityModel
            (
                password_hash:         $ukh->getPasswordHashed(),
                recoverykey_encrypted: $ukh->getRecoveryKeyEncrypted(),
                masterkey_salt:        $ukh->getMasterKeySalt(),
                id_user:               $_SESSION['ID_USER']
            );

            $status = $us->upd_pwdHash_rKeyEnc_mKeySalt_by_userID();

            session_destroy();
            
            if ($status === false)
                HttpResponse::serverError();
            else
                HttpResponse::successful();
        }
    }

?>