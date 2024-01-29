<?php

    define('__ROOT__', '../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/file_system_handler.php';
    require_once __ROOT__ . 'model/ds/token.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/ds/qry.php';
    require_once __ROOT__ . 'model/ds/functions.php';
    require_once __ROOT__ . 'model/ds/mail.php';
    require_once __ROOT__ . 'model/models/user.php';
    require_once __ROOT__ . 'model/models/verify.php';
    require_once __ROOT__ . 'model/models/usersecurity.php';
    require_once __ROOT__ . 'model/ds/google2FA.php';

    main();

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'POST': {
                    handle_post();
                    break;
                }
    
                default: {
                    http_response::client_error(405);
                }
            }
        }
        else
        {
            http_response::server_error(500);
        }
    }

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

                        $tkn = new token(100);

                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        $ver = new Verify();
                        $ver->init($tkn->hashed(), $user->get_id());

                        QRY::ins_verify($conn, $ver, __QP__);
                        MYPDO::close_connection($conn);

                        $mailer = new MyMail();

                        $url = $_ENV['DOMAIN'] . 'view/pages/signin/index.php?tkn=' . (string)$tkn;
                        $body = 'Click the link to confirm your email: ' . $url;
                        $obj = 'Secure-cloud: verify your email';

                        $mailer->send($user->get_email(), $obj, $body);

                        $secret_2fa = Google2FA::gen_rnd_secret();
                        
                        $user_sec = UserSecurity::get_user_sec(
                            $user->get_id(), 
                            $_POST['pwd'], 
                            $_POST['rkey'], 
                            $_POST['rkey_c'], 
                            $_POST['ckey_c'], 
                            $_POST['rkey_iv'], 
                            $_POST['ckey_iv'], 
                            $secret_2fa
                        );
                        
                        $conn = MYPDO::get_new_connection('USER_TYPE_INSERT', $_ENV['USER_TYPE_INSERT']);
                        QRY::ins_user_sec($conn, $user_sec, __QP__);
                        MYPDO::close_connection($conn);

                        session_start();
                        $_SESSION['VERIFY_PAGE_STATUS'] = 'SIGNUP_OK';

                        $redirect_url = $_ENV['DOMAIN'] . '/view/pages/verify/index.php';
                        http_response::successful(200, false, array("redirect" => $redirect_url));
                    }
                }
            }
        }   
    }

?>