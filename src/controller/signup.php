<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../../resource/mymail.php';
    require_once __DIR__ . '/../../resource/my_two_factor_auth.php';
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

                

            // ----------- BEGIN User CREATION -------------

            $user = new UserModel(email:$email, name:$name, surname:$surname);

            $email_is_taken = $user->email_is_taken();

            if ($email_is_taken === 1)
            {
                http_response::client_error(400, "Email already taken");
            }
            else if ($email_is_taken === 0)
            {
                // email is available
            }
            else
                http_response::server_error();

            mypdo::connect('insert');
            mypdo::begin_transaction();
            
            if (!$user->ins())
            {
                mypdo::roll_back();
                http_response::server_error(500);
            }
            
            // ----------- END User CREATION -------------


            
            // ----------- BEGIN User-Security CREATION -------------

            $user->sel_id_from_email();

            $user_keys = UserKeysHandler::get_instance_from_pwd($pwd);
            
            $user_security_data = new UserSecurityModel
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

            // ----------- END User-Security CREATION -------------




            // ----------- BEGIN Email-Verify CREATION -------------

            $email_sent = true;//EmailVerifyController::send_email_verify($user->get_email());

            if ($email_sent === false)
            {
                mypdo::roll_back();
                http_response::client_error(400, "There is an issue with the provided email address, it may not exist.");
            }
            
            // ----------- END Email-Verify CREATION -------------


            mypdo::commit();

            http_response::successful
            (
                201, 
                false, 
                array("redirect" => '/signup/success')
            );
        }
    }


?>
