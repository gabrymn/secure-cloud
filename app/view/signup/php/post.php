<?php

    require_once __ROOT__ . 'model/http/http_response.php';
    require_once __ROOT__ . 'model/file_system_handler.php';
    require_once __ROOT__ . 'model/token.php';
    require_once __ROOT__ . 'model/db/mypdo.php';
    require_once __ROOT__ . 'model/db/qry.php';
    require_once __ROOT__ . 'model/functions.php';
    require_once __ROOT__ . 'model/mail.php';
    require_once __ROOT__ . 'model/models/user.php';
    require_once __ROOT__ . 'model/models/verify.php';
    
    function handle_post(&$error)
    {
        if (key_contains($_POST, 'email', 'pwd', 'pwd2', 'name', 'surname'))
        {
            $user = new User();
            $user->init($_POST['email'], $_POST['pwd'], $_POST['name'], $_POST['surname']);
            handle_user_insertion($user, $error);
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }

    function handle_user_insertion($user, &$error)
    {
        if (!filter_var($user->get_email(), FILTER_VALIDATE_EMAIL))
        {
            http_response_code(400);
            $error = "Invalid email format";
        }
        else
        {        
            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
            $res = QRY::sel_id_from_email($conn, $user->get_email(), __QP__);
            MYPDO::close_connection($conn);
            
            if ($res !== -1)
            {
                http_response_code(400);
                $error =  "Email already taken";
            }   
            else
            {
                if (!MyMail::is_real($user->get_email()))
                {
                    http_response_code(400);
                    $error =  "Email does not exists";
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
                        http_response::server_error(500, "3Internal server error, try again");
                    }
                    else
                    {
                        $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                        $id_user = QRY::sel_id_from_email($conn, $user->get_email(), __QP__);
                        $user->set_id($id_user);
                        MYPDO::close_connection($conn);

                        $tkn = new token(256);

                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        $ver = new Verify();
                        $ver->init($tkn->hashed(), $user->get_id());
                        QRY::ins_verify($conn, $ver, __QP__);
                        MYPDO::close_connection($conn);

                        $mailobj = new MyMail();

                        $url = 'http://localhost/signin?tkn=' . $tkn->hashed();
                        $body = 'Click the link to confirm your email: ' . $url;
                        $obj = 'Secure-cloud: verify your email';

                        $mailobj->send($user->get_email(), $obj, $body);

                        session_start();
                        $_SESSION['EMAIL_VERIFING'] = true;

                        header("location:verify");
                        exit;
                    }
                }
            }
        }   
    }

?>
