<?php

    require_once __ROOT__ . 'model/http/http_response.php';
    require_once __ROOT__ . 'model/file_system_handler.php';
    require_once __ROOT__ . 'model/token.php';
    require_once __ROOT__ . 'model/mypdo.php';
    require_once __ROOT__ . 'model/qry.php';
    require_once __ROOT__ . 'model/functions.php';
    require_once __ROOT__ . 'model/mail.php';
    require_once __ROOT__ . 'model/models/user.php';
    require_once __ROOT__ . 'model/models/verify.php';
    require_once __ROOT__ . 'model/models/usersecurity.php';

    function handle_post()
    {
        if (count($_POST) === 2 && key_contains($_POST, 'email', 'pwd'))
        {
            htmlspecialchars_array($_POST);

            $user = new User;
            $user->set_email($_POST['email']);
            $user->set_pwd($_POST['pwd']);

            handle_user_signin($user);
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }

    function handle_user_signin(User $user)
    {
        // invalid email format
        if (!filter_var($user->get_email(), FILTER_VALIDATE_EMAIL))
        {
            http_response::client_error(400, "Invalid email format");
        }
        // valid email format
        else 
        {        
            // sel id user from email
            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
            $id_user = QRY::sel_id_from_email($conn, $user->get_email(), __QP__);
            
            // There's no email in db that is equals to $user->get_email()
            if ($id_user === -1)
            {
                MYPDO::close_connection($conn);
                http_response::client_error(400, "That email doesn't exists in our system");
            }   
            // email $user->get_email() exists in db
            else
            {
                $user->set_id($id_user);

                // get hashed pwd from db
                $pwd_hash = QRY::sel_pwd_from_id($conn, $id_user, __QP__);

                // there's no record in user_security that has that id_user, server error 
                if ($pwd_hash === -1)
                {
                    MYPDO::close_connection($conn);
                    http_response::server_error(500, "Something wrong, try again");
                }

                $us = new UserSecurity();
                $us->set_pwd_hash($pwd_hash);

                // credentials OK
                if (password_verify($user->get_pwd(), $us->get_pwd_hash()))
                {
                    session_start();

                    $verified = QRY::sel_verified_from_id($conn, $user->get_id(), __QP__);

                    // user is tryin to login without have verified the email 
                    if ($verified === -1)
                    {
                        $_SESSION['VERIFY_PAGE_STATUS'] = 'SIGNIN_WITH_EMAIL_NOT_VERIFIED';
                        $_SESSION['EMAIL'] = $user->get_email();

                        http_response::client_error(
                            400, 
                            "Confirm your email before signin", 
                            array("redirect" => $_ENV['DOMAIN'] . '/view/verify/verify.php')
                        );
                    }
                    // user is verified
                    else if ($verified === 1)
                    {
                        if (isset($_SESSION['VERIFY_PAGE_STATUS'])) unset($_SESSION['VERIFY_PAGE_STATUS']);

                        $_SESSION['AUTH_1FA'] = true;

                        // check if 2FA is setted
                        $p2fa = QRY::sel_2fa_from_id($conn, $user->get_id(), __QP__);

                        // No 2FA
                        if ($p2fa === -1)
                        {
                            $_SESSION['LOGGED'] = true;
                            $_SESSION['ID_USER'] = $user->get_id();

                            http_response::successful(
                                200, 
                                false, 
                                array("redirect" =>  $_ENV['DOMAIN'] . '/view/private/private.php')
                            );
                        }
                        
                        // User has 2FA active, invoke 2FA procedure
                        else if ($p2fa === 1)
                        {
                            // check code 2FA
                        }

                        // query error, server error
                        else
                        {
                            http_response::server_error(500);
                        }
                    }
                    // query error, server error
                    else
                    {
                        http_response::server_error(500);
                    }

                }
                // password is wrong
                else
                {
                    MYPDO::close_connection($conn);
                    http_response::client_error(400, "Password is wrong");
                }
            }
        }
    }
?>
