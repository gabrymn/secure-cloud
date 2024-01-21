<?php

    require_once __ROOT__ . 'model/http/http_response.php';
    require_once __ROOT__ . 'model/file_system_handler.php';
    require_once __ROOT__ . 'model/email.php';
    require_once __ROOT__ . 'model/token.php';
    require_once __ROOT__ . 'model/db/mypdo.php';
    require_once __ROOT__ . 'model/db/qry.php';
    require_once __ROOT__ . 'model/functions.php';
    
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
                http_response::server_error(500, "Internal server error, try again");
            
            $res = QRY::sel_id_from_email($conn, $email, __QP__);

            if (!$res)
                http_response::server_error(500, "Internal server error, try again");
            
            MYPDO::close_connection($conn);

            if (count($res[0]) > 0)
            {
                http_response_code(400);
                $error =  "Email already taken";
            }   
            else
            {
                $state = email::is_real($email);

                if (!$state)
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
                        http_response::server_error(500, "Internal server error, try again");
                    
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
                        http_response::server_error(500, "Internal server error, try again");

                    MYPDO::close_connection($conn);
                    
                    $user_folder_created = file_system_handler::mk_dir($email, __ROOT__ . 'users/');
                    
                    if ($user_folder_created === false)
                    {
                        // IF DIR HAS NOT BEEN CREATED DELETE ALL USER DATA FROM DB
                        
                        $conn = MYPDO::get_new_connection('USER_TYPE_DELETE', $_ENV['USER_TYPE_DELETE']);
                        if (!$conn)
                            http_response::server_error(500, "Internal server error, try again");
                        
                        $status = QRY::del_user_from_email($conn, ["email" => $email], __QP__);
                        if (!$status)
                            http_response::server_error(500, "Internal server error, try again");
                        
                        MYPDO::close_connection($conn);
                        http_response::server_error(500, "Internal server error, try again");
                    }
                    else
                    {
                        $tkn = new token(256);

                        $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                        if (!$conn)
                            http_response::server_error(500, "Internal server error, try again");

                        $id_user = QRY::sel_id_from_email($conn, $tkn, $email, __QP__);
                        if (!$id_user)
                            http_response::server_error(500, "Internal server error, try again");

                        QRY::ins_verify($conn, $id_user, $tkn->hashed(), __QP__);
            
                        $sub = "Secure-cloud: verify your email";
                        $link = "[DOMAIN]/signin?";
                        $link .= "tkn={$tkn->get()}";
                        
                        $msg = "Click this link: $link";
            
                        email::send($email, $sub, $msg);
                    }
                }
            }
        }   
    }

?>