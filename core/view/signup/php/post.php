<?php

    require_once __ROOT__ . 'model/http/http_response.php';
    require_once __ROOT__ . 'model/file_system_handler.php';
    require_once __ROOT__ . 'model/email.php';
    require_once __ROOT__ . 'model/db/mypdo.php';
    require_once __ROOT__ . 'model/db/qry.php';
    require_once '../functions.php';
    
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

            if (intval($res[0]) > 0)
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
                            
                    $user_data = [
                        "name" => $name, 
                        "surname" => $surname,
                        "email" => $email,
                        "pwd" => password_hash($pwd, PASSWORD_BCRYPT),
                        "2fa" => 0,
                        "verified" => 0
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
                        // TO DO:
                        /*
                            1) start session
                            2) insert confirm token record inside table account verify
                            3) send confirm email
                        */

                        /*
                        session_start();
                        $_SESSION['VERIFING_EMAIL'] = 1;

                        $token = new token(50, array("a-z", "A-Z", "0-9"));
    
                        sqlc::connect("USER_STD_SEL");
                        $id_user = sqlc::get_id_user($email);
                        sqlc::close();
                        sqlc::connect("USER_STD_INS");
                        sqlc::ins_tkn_verify(intval($id_user), $token->hashed());
                        sqlc::close();
            
                        $sub = "Secure-cloud: verify your email";

                        $link = "[DOMAIN]/signin.php?";
                        $link .= "tkn={$token->get()}";
                        
                        $msg = "Click this link: $link";
            
                        email::send($email, $sub, $msg, $red);
                        */
                    }
                }
            }
        }   
    }

?>