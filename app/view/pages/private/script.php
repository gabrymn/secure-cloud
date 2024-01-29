<?php
    
    define('__ROOT__', '../../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/google2FA.php';
    require_once __ROOT__ . 'model/ds/qry.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    
                    session_start();

                    if (!isset($_SESSION['LOGGED']) || !isset($_SESSION['ID_USER']))
                    {
                        http_response::client_error(401);
                    }
                    else
                    {
                        $id_user = $_SESSION['ID_USER'];

                        $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                        $secret_2fa = QRY::sel_secret_2fa_from_id($conn, $id_user, __QP__);
                        $email = QRY::sel_email_from_id($conn, $id_user, __QP__);
                        MYPDO::close_connection($conn);

                        $g = new Google2FA($email, $secret_2fa);

                        return array($g, $secret_2fa);
                    }

                    break;
                }

                default: {
                    http_response::client_error(405);
                }
            }
        }
        else http_response::server_error(500);
    }


?>