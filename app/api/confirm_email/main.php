<?php

    define('__ROOT__', '../../');
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once 'get.php';

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

    main();

?>