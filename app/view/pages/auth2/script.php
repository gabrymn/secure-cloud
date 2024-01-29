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
    
                    if (!isset($_SESSION['OTP_CHECKING']))
                    {
                        http_response::client_error(401);
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