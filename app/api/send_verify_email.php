<?php

    define('__ROOT__', '../');
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/token.php';
    require_once __ROOT__ . 'model/models/verify.php';
    require_once __ROOT__ . 'model/models/user.php';
    require_once __ROOT__ . 'model/ds/mail.php';
    require_once __ROOT__ . 'model/ds/qry.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';

    main();

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    handle_get();
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
            http_response::client_error(401);
        }

        $user = new User();
        $user->set_email($_SESSION['EMAIL']);

        if (send_verify_email($user))
        {
            $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT_NF';
            unset($_SESSION['EMAIL']);

            header("location:". $_ENV['DOMAIN'] . '/view/pages/verify/index.php');
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

        $url = $_ENV['DOMAIN'] . 'view/pages/signin/index.php?tkn=' . (string)$tkn;
        $body = 'Click the link to confirm your email: ' . $url;
        $obj = 'Secure-cloud: verify your email';

        return $mailer->send($user->get_email(), $obj, $body);
    }

?>