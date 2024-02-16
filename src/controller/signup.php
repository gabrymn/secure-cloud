<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_system_handler.php';
    require_once __DIR__ . '/../../resource/token.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../../resource/mail.php';
    require_once __DIR__ . '/../../resource/two_factor_auth.php';
    require_once __DIR__ . '/../../resource/user_keys_handler.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/user_security.php';

    class SignupController
    {
        public static function render_signup_page()
        {
            include __DIR__ . '/../view/dynamic/signup.php';
        }

        public static function process_signup($email, $pwd, $name, $surname)
        {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                http_response::client_error(400, "Invalid email format");

            if (strlen($_POST['pwd']) < 1)
                http_response::client_error(400, "Password too short");
            
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

            file_system_handler::mk_user_storage_dir($user->get_email(), __DIR__ . '/../../users_storage/');
            
            $tkn = new token(100);
            $e_verify = new EmailVerify
            (
                tkn_hash: $tkn->hashed(), 
                id_user: $user->get_id()
            );

            if (!$e_verify->ins())
            {
                mypdo::roll_back();
                http_response::server_error();
            }

            /*$email_sent = MyMail::send_email_verify($user->get_email(), (string)$tkn);
            if ($email_sent === false)
                http_response::client_error(400, "There is an issue with the provided email address, it may not exist.");
            */

            $user_keys = UserKeysHandler::get_instance_from_pwd($pwd);
            
            $user_security_data = new UserSecurity
            (
                pwd_hash:               $user_keys->get_pwd_hashed(),
                rkey_hash:              $user_keys->get_rkey_hashed(),
                rkey_encrypted:         $user_keys->get_rkey_encrypted(),
                ckey_encrypted:         $user_keys->get_ckey_encrypted(),
                secret_2fa_encrypted:   $user_keys->get_secret_2fa_encrypted(),
                dkey_salt:              $user_keys->get_dkey_salt(),
                id_user:                $user->get_id()
            );

            if (!$user_security_data->ins())
            {
                mypdo::roll_back();
                http_response::server_error();
            }

            mypdo::commit();

            session_start();

            $_SESSION['VERIFY_PAGE_STATUS'] = 'SIGNUP_OK';
        
            $redirect_url = $_ENV['DOMAIN'] . '/verify';
                
            http_response::successful(200, false, array("redirect" => $redirect_url));
        }
    }


?>
