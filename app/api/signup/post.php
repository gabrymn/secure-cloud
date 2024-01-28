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

        if (count($_POST) === 9 && key_contains($_POST, 'email', 'name', 'surname', 'pwd', 'rkey', 'rkey_c', 'ckey_c', 'rkey_iv', 'ckey_iv'))
        {
            htmlspecialchars_array($_POST);

            $user = new User;
            $user->init($_POST['email'], $_POST['name'], $_POST['surname']);
            handle_user_insertion($user);
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }

    function handle_user_insertion($user)
    {
        // EMAIL,PWD server check
        if (!filter_var($user->get_email(), FILTER_VALIDATE_EMAIL))
        {
            http_response::client_error(400, "Invalid email format");
        }
        else if (strlen($_POST['pwd']) < 1)
        {
            http_response::client_error(400, "Password too short");
        }
        else
        {        
            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
            $res = QRY::sel_id_from_email($conn, $user->get_email(), __QP__);
            MYPDO::close_connection($conn);
            
            if ($res !== -1)
            {
                http_response::client_error(400, "Email already taken");
            }   
            else
            {
                if (!MyMail::is_real($user->get_email()))
                {
                    http_response::client_error(400, "Email does not exists");
                }
                else 
                {
                    $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                    QRY::ins_user($conn, $user, __QP__);
                    MYPDO::close_connection($conn);
                    
                    $user_folder_created = file_system_handler::mk_dir($user->get_email(), __ROOT__ . 'users/');
                    
                    if ($user_folder_created === false)
                    {
                        // IF DIR HAS NOT BEEN CREATED DELETE ALL USER DATA FROM DB
                        $conn = MYPDO::get_new_connection('USER_TYPE_DELETE', $_ENV['USER_TYPE_DELETE']);
                        QRY::del_user_from_email($conn, $user->get_email(), __QP__);
                        MYPDO::close_connection($conn);
                        http_response::server_error(500, "__Internal server error, try again");
                    }
                    else
                    {
                        $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                        $id_user = QRY::sel_id_from_email($conn, $user->get_email(), __QP__);
                        MYPDO::close_connection($conn);

                        $user->set_id($id_user);

                        $user_sec = UserSecurity::get_user_sec($user->get_id(), $_POST['pwd'], $_POST['rkey'], $_POST['rkey_c'], $_POST['ckey_c'], $_POST['rkey_iv'], $_POST['ckey_iv']);

                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        QRY::ins_user_sec($conn, $user_sec, __QP__);
                        MYPDO::close_connection($conn);

                        $tkn = new token(100);

                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        $ver = new Verify();
                        $ver->init($tkn->hashed(), $user->get_id());

                        QRY::ins_verify($conn, $ver, __QP__);
                        MYPDO::close_connection($conn);

                        $mailer = new MyMail();

                        $url = 'http://localhost/view/signin/signin.php?tkn=' . (string)$tkn;
                        $body = 'Click the link to confirm your email: ' . $url;
                        $obj = 'Secure-cloud: verify your email';

                        $mailer->send($user->get_email(), $obj, $body);

                        session_start();
                        $_SESSION['EMAIL_VERIFING'] = true;

                        $redirect_url = $_ENV['DOMAIN'] . '/view/verify/verify.php';
                        http_response::successful(200, false, array("redirect" => $redirect_url));
                    }
                }
            }
        }   
    }

?>
