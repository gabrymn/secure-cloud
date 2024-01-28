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
        session_start();

        if (!isset($_SESSION['EMAIL']) && !isset($_SESSION['VERIFY_PAGE_STATUS']))
        {
            session_destroy();
            http_response::client_error(401);
        }

        if ($_SESSION['VERIFY_PAGE_STATUS'] !== 'SIGNIN_WITH_EMAIL_NOT_VERIFIED')
        {
            session_destroy();
            unset($_SESSION['VERIFY_PAGE_STATUS']);
            unset($_SESSION['EMAIL']);
            http_response::client_error(500, "wtf?");
        }

        $user = new User();
        $user->set_email($_SESSION['EMAIL']);

        if (send_verify_email($user))
        {
            $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT_NF';
            unset($_SESSION['EMAIL']);

            header("location:". $_ENV['DOMAIN'] . '/view/verify/verify.php');
            exit;
        }
        else
            http_response::server_error(500);
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