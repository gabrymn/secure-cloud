<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../../resource/mail.php';
    require_once __DIR__ . '/../../resource/two_factor_auth.php';
    require_once __DIR__ . '/../../resource/user_keys_handler.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/user_security.php';
    
    class SignupController
    {
        public static function render_signup_page()
        {
            $navbar = Navbar::getPublic('signup');
            include __DIR__ . '/../view/signup.php';
        }

        public static function render_signup_success_page()
        {
            include __DIR__ . '/../view/static/signup_success.php';
        }

        public static function process_signup($email, $pwd, $name, $surname)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                http_response::client_error(400, "Invalid email format");

            if (strlen($pwd) < 1)
                http_response::client_error(400, "Password too short");

                

            // ----------- Start User creation -------------

            $user = new User(email:$email, name:$name, surname:$surname);

            $id_user = $user->sel_id_from_email();

            if ($id_user === false)
                http_response::server_error();
            
            if ($id_user !== -1)
                http_response::client_error(400, "Email already taken");
            
            unset($id_user);

            mypdo::connect('insert');
            mypdo::begin_transaction();
            
            if (!$user->ins())
            {
                mypdo::roll_back();
                http_response::server_error(500);
            }

            $user->sel_id_from_email();
            
            FileSysHandler::mk_user_storage_dir($user->get_id_user(), $user->get_email());

            // ----------- End User creation -------------



            // ----------- Start User-Security creation -------------

            $user_keys = UserKeysHandler::get_instance_from_pwd($pwd);

            $user_security_data = new UserSecurity
            (
                pwd_hash:               $user_keys->get_pwd_hashed(),
                rkey_hash:              $user_keys->get_rkey_hashed(),
                rkey_encrypted:         $user_keys->get_rkey_encrypted(),
                ckey_encrypted:         $user_keys->get_ckey_encrypted(),
                secret_2fa_encrypted:   $user_keys->get_secret_2fa_encrypted(),
                dkey_salt:              $user_keys->get_dkey_salt(),
                id_user:                $user->get_id_user()
            );

            if (!$user_security_data->ins())
            {
                mypdo::roll_back();
                http_response::server_error();
            }

            // ----------- End User-Security creation -------------




            // ----------- Start Email-Verify creation -------------

            $email_sent = EmailVerifyController::send_email_verify($user->get_email());

            if ($email_sent === false)
            {
                mypdo::roll_back();
                http_response::client_error(400, "There is an issue with the provided email address, it may not exist.");
            }
            
            // ----------- End Email-Verify creation -------------


            mypdo::commit();

            http_response::successful(201, false, array("redirect" => '/signup/success'));
        }
    }


?>
