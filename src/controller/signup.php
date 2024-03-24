<?php

    require_once __DIR__ . '/../../utils/httpkit/http_response.php';
    require_once __DIR__ . '/../../utils/file_sys_handler.php';
    require_once __DIR__ . '/../../utils/mypdo.php';
    require_once __DIR__ . '/../../utils/mymail.php';
    require_once __DIR__ . '/../../utils/securekit/crypto_rnd_string.php';
    require_once __DIR__ . '/../../utils/securekit/my_tfa.php';
    require_once __DIR__ . '/../model/user_keys_handler.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/user_secrets.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    
    class SignupController
    {
        private const PASSWORD_MIN_LEN = 2;
        private const PASSWORD_REGEX = "^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$";
        private const NAME_MIN_LEN = 2;

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
            // Server side inputs validation (input sanitization have already been done in /utils/httpkit/router class)
            // ----------------------------------------------------------------

            if (self::emailValidation($email) === false)
                httpResponse::clientError(400, "Invalid email format");

            if (self::passwordValidation($pwd) === false)
                httpResponse::clientError(400, "Invalid password format");

            if (self::nameValidation($name) === false)
                httpResponse::clientError(400, "Invalid name format");
    
            if (self::nameValidation($surname) === false)
                httpResponse::clientError(400, "Invalid surname format");
            // ----------------------------------------------------------------

                

            // ----------- BEGIN User CREATION -------------

            $user = new UserModel(email:$email, name:$name, surname:$surname);

            $email_is_taken = $user->email_is_taken();

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

            MyPDO::connect($_ENV['EDIT_USERNAME'], $_ENV['EDIT_PASSWORD'], $_ENV['DB_HOST'], $_ENV['DB_NAME']);
            MyPDO::beginTransaction();
            
            if (!$user->ins())
            {
                MyPDO::rollBack();
                httpResponse::serverError();
            }

            $user->sel_userID_by_email();

            if (self::createUserDirs($user) === false)
            {
                MyPDO::rollBack();
                HttpResponse::serverError();
            }
            
            // ----------- END User CREATION -------------


            
            // ----------- BEGIN User-Secrets CREATION -------------


            $user_keys = UserKeysHandler::getInstanceFromPassword($pwd);
            
            $user_secrets_data = new UserSecretsModel
            (
                password_hash:         $user_keys->getPasswordHashed(),
                recoverykey_hash:      $user_keys->getRecoveryKeyHashed(),
                recoverykey_encrypted: $user_keys->getRecoveryKeyEncrypted(),
                cipherkey_encrypted:   $user_keys->getCipherKeyEncrypted(),
                secret2fa_encrypted:   $user_keys->getSecret2FAEncrypted(),
                masterkey_salt:        $user_keys->getMasterKeySalt(),
                id_user:               $user->getUserID()
            );

            if (!$user_secrets_data->ins())
            {
                MyPDO::rollBack();
                httpResponse::serverError();
            }

            // ----------- END User-Secrets CREATION -------------




            // ----------- BEGIN Email-Verify CREATION -------------

            $email_sent = true;//EmailVerifyController::send_email_verify($user->get_email());

            if ($email_sent === false)
            {
                MyPDO::rollBack();
                httpResponse::clientError(400, "There is an issue with the provided email address, it may not exist.");
            }
            
            // ----------- END Email-Verify CREATION -------------

            


            // Transaction OK
            MyPDO::commit();

            httpResponse::successful
            (
                status_code:    201, 
                response_array: ["redirect" => '/signup/success']
            );
        }


        /**
         * Creates a directory structure for a user in the storage system.
         *
         * This function creates a directory structure for a user, including the main storage directory
         * and a subdir for the user data and temp dirs for uploads, and downloads. 
         * The main directory is created by hashing the userID and email using SHA-256.
         *
         * @param UserModel $user
         *
         * @return bool Returns true if the directory structure is successfully created,
         *              false on failure.
         *
         * @throws Exception If any issues arise during directory creation.
         */
        private static function createUserDirs(UserModel $user)
        {
            $root_storage_dir = __DIR__ . '/../../storage';

            if (!is_dir($root_storage_dir))
                mkdir($root_storage_dir);

            $user_dir_root = $root_storage_dir . '/' . $user->getDirName();

            $user_dir_data = $user_dir_root . '/' . UserModel::DATA_DIRNAME;
            $user_dir_uploads = $user_dir_root . '/' . UserModel::UPLOADS_DIRNAME;
            $user_dir_downloads = $user_dir_root . '/' . UserModel::DOWNLOADS_DIRNAME;

            return
            (
                mkdir($user_dir_root) &&
                mkdir($user_dir_data) &&
                mkdir($user_dir_uploads) &&
                mkdir($user_dir_downloads)
            );
        }

        public static function emailValidation($email) : bool
        {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        public static function passwordValidation($password)
        {
            return strlen($password) >= self::PASSWORD_MIN_LEN;
        }   

        private static function nameValidation($name)
        {
            return strlen($name) >= self::NAME_MIN_LEN;
        }  
    }


?>
