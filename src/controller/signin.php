<?php


    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../../resource/mydatetime.php';
    require_once __DIR__ . '/../../resource/client.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/email_verify.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    
    class SigninController
    {
        public static function render_signin_page($success_msg = "", $error_msg = "")
        {
            $navbar = Navbar::getPublic('signin');
            include __DIR__ . "/../view/signin.php";
        }

        public static function process_signin($email, $pwd)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                http_response::client_error(400, "Invalid email format");


            
            // ------------ BEGIN User PROCESS -----------

            $user = new UserModel(email:$email);

            // There's no email in db that is equals to $user->get_email()
            if ($user->sel_id_from_email() === -1)
                http_response::client_error(400, "That email doesn't exists in our system");

            // ------------ END User PROCESS -----------



            // ------------ BEGIN UserSecurity PROCESS -----------

            $us = new UserSecurityModel(id_user: $user->get_id_user());
            
            // there's no record in user_security that has that id_user, server error 
            if (!$us->sel_pwd_hash_from_id())
                http_response::server_error(500, "Something wrong, try again");

            // password is wrong (1FA FAILED)
            if (!password_verify($pwd, $us->get_pwd_hash()))
                http_response::client_error(400, "Password is wrong");

            // ------------ END UserSecurity PROCESS -----------

            


            // ------------ BEGIN EmailVerify PROCESS -----------

            session_start();

            $id_user = $user->sel_verified_from_id();

            if ($id_user === false || $id_user === null)
            {
                session_destroy();
                http_response::server_error();
            }

            // user is tryin' to signin without have verified the email 
            else if ($id_user === 0)
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'SIGNIN_WITH_EMAIL_NOT_VERIFIED';
                $_SESSION['EMAIL'] = $user->get_email();

                http_response::client_error
                (
                    400, 
                    "Confirm your email before sign in", 
                    array("redirect" => '/verify')
                );
            }

            // user is verified
            else
            {
                $_SESSION['AUTH_1FA'] = true;
                $_SESSION['ID_USER'] = $user->get_id_user();

                if (isset($_SESSION['VERIFY_PAGE_STATUS'])) unset($_SESSION['VERIFY_PAGE_STATUS']);
            }




            // ------------ END EmailVerify PROCESS -----------





            // ------------ BEGIN 2FA PROCESS -----------


            // check if 2FA isset

            if ($user->sel_2fa_from_id() === null)
            {
                session_destroy();
                http_response::server_error(500);
            }

            // Set DKEY (a key derived from password and a random salt) to a session variable

            $us->set_id_user($user->get_id_user());
            $dkey_salt = $us->sel_dkey_salt_from_id();
            $_SESSION['DKEY'] = crypto::deriveKey($pwd, $dkey_salt);


            // If the user has 2FA active, redirect to 2FA page
            if ($user->get_p2fa() === 1)
            {
                $_SESSION['OTP_CHECKING'] = true;

                http_response::successful
                (
                    200, 
                    false, 
                    array("redirect" =>  '/auth2')
                );
            }

            // No 2FA, login ok

            $_SESSION['LOGGED'] = true;


            // ------------ END 2FA PROCESS -----------





            // check if there is an active session with the client IP

            SessionModel::create_or_load(client::get_ip(), $user->get_id_user());

            http_response::successful
            (
                200, 
                false, 
                array("redirect" =>  '/clouddrive')
            );
        }

        public static function process_signout()
        {
            if (session_status() == PHP_SESSION_NONE)                
                session_start();

            $_SESSION = [];
            session_destroy();

            http_response::redirect('/signin');
        }
    }

?>