<?php

    define('__ROOT__', '../');
    
    require_once __ROOT__ . '/model/http/http_response.php';

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET': {
                
                session_start();

                if (!isset($_SESSION['LOGGED']))
                    http_response::client_error(401, "you cannot logout since you are not logged in");
                else
                    logout();

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

    function logout()
    {
        session_destroy();
        header("location: " . $_ENV['DOMAIN'] . '/view/signin/signin.php');
        exit;
    }

?>