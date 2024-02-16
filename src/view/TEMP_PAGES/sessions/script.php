<?php

    define('__ROOT__', '../../../'); 
    
    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/two_factor_auth.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/ds/client.php';
    require_once __ROOT__ . 'model/ds/mydatetime.php';
    require_once __ROOT__ . 'model/models/user_security.php';
    require_once __ROOT__ . 'model/models/session.php';

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {

                    session_start();
                    check_auth();

                    break;
                }

                default: {
                    http_response::client_error(405);
                }
            }
        }
        else http_response::server_error(500);
    }


    function check_auth()
    {
        if (!isset($_SESSION['LOGGED']))
        {
            session_destroy();
            header("Location: " . $_ENV['DOMAIN'] . '/view/pages/signin/index.php');
            exit;
        }

        $s = new Session
        (
            id_session: $_SESSION['CURRENT_ID_SESSION'], 
            id_user: $_SESSION['ID_USER']
        );
        
        $session_expired = $s->is_expired_from_idsess();

        if ($session_expired)
        {
            session_destroy();
            header("Location: " . $_ENV['DOMAIN'] . '/view/pages/signin/index.php');
            exit;
        }
    }

?>