<?php

    require_once 'get.php';
    require_once __ROOT__ . 'model/http/http_response.php';
    
    function main(&$success, &$error, &$redirect)
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    handle_get($success, $error, $redirect);
                    break;
                }

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

?>