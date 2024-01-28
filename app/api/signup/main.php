<?php

    define('__ROOT__', '../../');
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once 'post.php';
    require_once __ROOT__ . 'model/http/http_response.php';
    
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

    main();

?>