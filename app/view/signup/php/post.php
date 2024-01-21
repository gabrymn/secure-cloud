<?php

    require_once __ROOT__ . 'model/http/http_response.php';
    require_once __ROOT__ . 'model/file_system_handler.php';
    require_once __ROOT__ . 'model/token.php';
    require_once __ROOT__ . 'model/db/mypdo.php';
    require_once __ROOT__ . 'model/db/qry.php';
    require_once __ROOT__ . 'model/functions.php';
    require_once __ROOT__ . 'model/mail.php';
    
    function handle_post(&$error)
    {
        if (key_contains($_POST, 'email', 'pwd', 'pwd2', 'name', 'surname'))
        {
            handle_user_insertion($error);
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }

    function handle_user_insertion(&$error)
    {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            unset($_POST['email']);
            unset($email);
            http_response_code(400);
            $error = "Invalid email format";
        }
        else
        {        
            $email = htmlspecialchars($_POST['email']);            
            $pwd = htmlspecialchars($_POST['pwd']);
            
            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
            
            if (!$conn)
                http_response::server_error(500, "99Internal server error, try again");
            
            $res = QRY::sel_id_from_email($conn, $email, __QP__);

            if (!$res)
                http_response::server_error(500, "88Internal server error, try again");
            
            MYPDO::close_connection($conn);
            
            if ($res !== -1)
            {
                http_response_code(400);
                $error =  "Email already taken";
            }   
            else
            {
                if (!MyMail::is_real($email))
                {
                    http_response_code(400);
                    $error =  "Email does not exists";
                }
                else 
                {
                    $name = htmlspecialchars($_POST['name']);
                    $surname = htmlspecialchars($_POST['surname']);

                    $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                    if (!$conn)
                        http_response::server_error(500, "77Internal server error, try again");
                    
                    $p2fa = 0;
                    $verified = 0;
                    
                    $user_data = [
                        $name, 
                        $surname,
                        $email,
                        password_hash($pwd, PASSWORD_BCRYPT),
                        $p2fa,
                        $verified
                    ];

                    $status = QRY::ins_user($conn, $user_data, __QP__);
                    
                    if (!$status)
                        http_response::server_error(500, "66Internal server error, try again");

                    MYPDO::close_connection($conn);
                    
                    $user_folder_created = file_system_handler::mk_dir($email, __ROOT__ . 'users/');
                    
                    if ($user_folder_created === false)
                    {
                        // IF DIR HAS NOT BEEN CREATED DELETE ALL USER DATA FROM DB

                        $conn = MYPDO::get_new_connection('USER_TYPE_DELETE', $_ENV['USER_TYPE_DELETE']);
                        if (!$conn)
                            http_response::server_error(500, "1Internal server error, try again");
                        
                        $status = QRY::del_user_from_email($conn, $email, __QP__);
                        if (!$status)
                            http_response::server_error(500, "2Internal server error, try again");
                        
                        MYPDO::close_connection($conn);
                        http_response::server_error(500, "3Internal server error, try again");
                    }
                    else
                    {
                        $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                        if (!$conn)
                            http_response::server_error(500, "4Internal server error, try again");
                        
                        $id_user = QRY::sel_id_from_email($conn, $email, __QP__);
                        
                        if (!$id_user)
                            http_response::server_error(500, "5Internal server error, try again");

                        MYPDO::close_connection($conn);

                        $tkn = new token(256);

                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        if (!$conn)
                            http_response::server_error(500, "6Internal server error, try again");

                        $status = QRY::ins_verify($conn, $tkn->hashed(), $id_user, __QP__);
                        if (!$status)
                            http_response::server_error(500, "612Internal server error, try again");

                        $mailobj = new MyMail(
                            $_ENV['EMAIL_USERNAME'],
                            $_ENV['EMAIL_PASSWORD'],
                            $_ENV['EMAIL_HOST']
                        );

                        $body = $mailobj->get_confirm_email_body($_ENV['CONFIRM_EMAIL_URL'], $_ENV['CONFIRM_EMAIL_BODY'], $tkn->hashed());

                        $mailobj->send($email, $_ENV['CONFIRM_EMAIL_OBJ'], $body);

                        header("location:signin");
                    }
                }
            }
        }   
    }

?>
