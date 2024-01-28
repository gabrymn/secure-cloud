<?php

    require_once __ROOT__ . '/model/http/http_response.php';
    require_once __ROOT__ . '/model/token.php';
    require_once __ROOT__ . '/model/models/verify.php';
    require_once __ROOT__ . '/model/models/user.php';
    require_once __ROOT__ . '/model/mail.php';
    require_once __ROOT__ . '/model/qry.php';
    require_once __ROOT__ . '/model/mypdo.php';

    function handle_get()
    {
        if (count($_GET) === 1 && isset($_POST['EMAIL']))
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

    function send_verify_email(User &$user)
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

        $url = 'http://localhost/view/signin/signin.php?tkn=' . (string)$tkn;
        $body = 'Click the link to confirm your email: ' . $url;
        $obj = 'Secure-cloud: verify your email';

        return $mailer->send($user->get_email(), $obj, $body);
    }

?>