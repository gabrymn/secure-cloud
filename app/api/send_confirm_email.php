<?php

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
        if (count($_POST) === 1 && isset($_POST['EMAIL']))
        {
            htmlspecialchars_array($_POST);

            $user = new User();
            $user->set_email($_POST['EMAIL']);

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

            $url = 'http://localhost/view/signin/signin.php?tkn=' . (string)$tkn;
            $body = 'Click the link to confirm your email: ' . $url;
            $obj = 'Secure-cloud: verify your email';

            $mailer->send($user->get_email(), $obj, $body);

            http_response::successful(200, "verify email sent");
        }
        else 
        { 
            http_response::client_error(404); 
        }
    }


?>