<?php

    require_once __DIR__ . '/../../resource/http/http_response.php';
    require_once __DIR__ . '/../../resource/storage/file_system_handler.php';
    require_once __DIR__ . '/../../resource/security/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/storage/mypdo.php';
    require_once __DIR__ . '/../../resource/mymail.php';
    require_once __DIR__ . '/../../resource/security/my_two_factor_auth.php';
    require_once __DIR__ . '/../../resource/security/user_keys_handler.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/user_security.php';
    
    class SignupController
    {
        public static function renderSignupPage()
        {
            $navbar = Navbar::getPublic('signup');
            include __DIR__ . '/../view/signup.php';
        }

        public static function renderSignupSuccessPage()
        {
            include __DIR__ . '/../view/static/signup_success.php';
        }

        public static function processSignup($email, $pwd, $name, $surname)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                httpResponse::clientError(400, "Invalid email format");

            if (strlen($pwd) < 1)
                httpResponse::clientError(400, "Password too short");

                

            // ----------- BEGIN User CREATION -------------

            $user = new UserModel(email:$email, name:$name, surname:$surname);

            $email_is_taken = $user->emailIsTaken();

            if ($email_is_taken === 1)
            {
                httpResponse::clientError(400, "Email already taken");
            }
            else if ($email_is_taken === 0)
            {
                // email is available
            }
            else
                httpResponse::serverError();

            MyPDO::connect('insert');
            MyPDO::beginTransaction();
            
            if (!$user->ins())
            {
                MyPDO::rollBack();
                httpResponse::serverError(500);
            }
            
            // ----------- END User CREATION -------------


            
            // ----------- BEGIN User-Security CREATION -------------

            $user->selIDFromEmail();

            $user_keys = UserKeysHandler::getInstanceFromPassword($pwd);
            
            $user_security_data = new UserSecurityModel
            (
                password_hash:         $user_keys->getPasswordHashed(),
                recoverykey_hash:      $user_keys->getRecoveryKeyHashed(),
                recoverykey_encrypted: $user_keys->getRecoveryKeyEncrypted(),
                cipherkey_encrypted:   $user_keys->getCipherKeyEncrypted(),
                secret2fa_encrypted:   $user_keys->getSecret2FA_encrypted(),
                masterkey_salt:        $user_keys->getMasterKeySalt(),
                id_user:               $user->getUserID()
            );

            if (!$user_security_data->ins())
            {
                MyPDO::rollBack();
                httpResponse::serverError();
            }

            // ----------- END User-Security CREATION -------------




            // ----------- BEGIN Email-Verify CREATION -------------

            $email_sent = true;//EmailVerifyController::send_email_verify($user->get_email());

            if ($email_sent === false)
            {
                MyPDO::rollBack();
                httpResponse::clientError(400, "There is an issue with the provided email address, it may not exist.");
            }
            
            // ----------- END Email-Verify CREATION -------------

            MyPDO::commit();

            FileSysHandler::makeUserDir($user->getUserID(), $user->getEmail());

            httpResponse::successful
            (
                201, 
                false, 
                array("redirect" => '/signup/success')
            );
        }
    }


?>
