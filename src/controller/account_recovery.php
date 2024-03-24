<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user_secrets.php';
    require_once __DIR__ . '/../model/user_keys_handler.php';
    require_once __DIR__ . '/../../utils/httpkit/http_response.php';
    require_once __DIR__ . '/signup.php';

    class AccountRecoveryController
    {
        public static function renderRecoverPage()
        {
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/recover.php';
        }

        public static function processRecoveryKeyCheck($email, $recovery_key)
        {
            if (SignupController::emailValidation($email) === false)
                HttpResponse::clientError(400, "Invalid email format");

            $user = new UserModel(email: $email);

            $user_id = $user->sel_userID_by_email($user->getEmail());

            if ($user_id === false)
                HttpResponse::serverError(500);
    
            if ($user_id === -1)
                HttpResponse::clientError(400, "The provided email does nox exists in our systems");

            $us = new UserSecretsModel(id_user: $user->getUserID());

            if (!password_verify($recovery_key, $us->sel_rKeyHash_by_userID()))
                HttpResponse::clientError(400, "The provided recovery key is incorrect. Please double-check the key and try again.");
            
            session_start();
            
            $_SESSION['RECOVERING_ACCOUNT'] = true;
            $_SESSION['ID_USER'] = $user->getUserID();
            $_SESSION['RECOVERY_KEY'] = $recovery_key;
    
            HttpResponse::successful(200);
        }

        public static function processPasswordReset($password)
        {
            if (SignupController::passwordValidation($password) === false)
                HttpResponse::clientError(400, "Invalid email format");

            $ukh = UserKeysHandler::getInstanceFromPassword($password);
            $ukh->setRecoveryKey($_SESSION['RECOVERY_KEY']);

            $us = new UserSecretsModel
            (
                password_hash:         $ukh->getPasswordHashed(),
                recoverykey_encrypted: $ukh->getRecoveryKeyEncrypted(),
                masterkey_salt:        $ukh->getMasterKeySalt(),
                id_user:               $_SESSION['ID_USER']
            );

            $status = $us->upd_pwdHash_rKeyEnc_mKeySalt_by_userID();

            session_destroy();
            
            if ($status === false)
                HttpResponse::serverError(500);
            else
                HttpResponse::successful(200);
        }
    }

?>