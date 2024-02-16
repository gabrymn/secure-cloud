<?php

    define('__ROOT__', '../');
    
    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/models/user_security.php';
    require_once __ROOT__ . 'model/models/session.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/ds/client.php';
    require_once __ROOT__ . 'model/ds/crypto.php';
    require_once __ROOT__ . 'model/ds/functions.php';
    
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
            // server error...
            http_response::server_error(500);
        }
    }

    function handle_get()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) 
            session_start();

        if ((isset($_SESSION['LOGGED']) && isset($_SESSION['ID_USER'])) === false)
        {
            session_destroy();
            http_response::client_error(401);
        }

        else if (count($_GET) === 1 && key_contains($_GET, 'recoverykey'))
        {
            $us = new UserSecurity(id_user: $_SESSION['ID_USER']);

            $us->set_rkey_encrypted($us->sel_rkey_from_id());

            $rkey = crypto::decrypt_AES_GCM($us->get_rkey_encrypted(), $_SESSION['DKEY']);

            http_response::successful(200, "ok", array("recoverykey" => $rkey));
        }
    }   

?>