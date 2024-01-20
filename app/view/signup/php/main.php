<?php

    require_once 'get.php';
    require_once 'post.php';

    function main(&$error)
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'POST': {
                    handle_post($error);
                    break;
                }
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

?>