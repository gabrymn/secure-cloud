<?php

    define('__ROOT__', '../../../'); 

    require_once __ROOT__ . 'model/ds/functions.php';
    require_once __ROOT__ . 'model/ds/http_response.php';

    function main()
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    logged_check();
                    break;
                }
    
                default: {
                    http_response::client_error(405);
                    break;
                }
            }
        }
        else
        {
            http_response::server_error(500);
        }
    }

?>