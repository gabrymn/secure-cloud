<?php

    define('__ROOT__', '../../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/functions.php';
    require_once __ROOT__ . 'model/models/verify.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/ds/token.php';
    require_once __ROOT__ . 'model/ds/qry.php';

    function main(&$success, &$error, &$redirect)
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    handle_req($success, $error, $redirect);
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

    function handle_req(&$success, &$error, &$redirect)
    {
        if (count($_GET) === 1 && key_contains($_GET, 'tkn'))
        {
            $tkn = new Token;
            $tkn->set($_GET['tkn']);

            $ver = new Verify();
            $ver->set_token($tkn->hashed());
            
            $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);

            $id_user = QRY::sel_id_from_tkn($conn, $ver->get_token(), __QP__);

            $ver->set_id_user($id_user);

            MYPDO::close_connection($conn);

            if ($ver->get_id_user() !== -1)
            {
                if (!session_status()) 
                    session_start();
                if (isset($_SESSION['VERIFING_EMAIL']))
                    unset($_SESSION['VERIFING_EMAIL']);

                $conn = MYPDO::get_new_connection('USER_ADMIN', $_ENV['USER_ADMIN']);
                QRY::upd_user_verified($conn, $ver->get_id_user(), __QP__);
                MYPDO::close_connection($conn);

                $conn = MYPDO::get_new_connection('USER_ADMIN', $_ENV['USER_ADMIN']);
                QRY::del_ver_from_tkn($conn, $ver->get_token(), $ver->get_id_user(), __QP__);
                MYPDO::close_connection($conn);

                $success = "Email verified, login";
            }
            else
            {
                http_response_code(400);
                $error = "Invalid or expired email verify link.";
            }
            
            $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';
        }
    }


?>