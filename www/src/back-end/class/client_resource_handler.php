<?php

    require_once "response.php";

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
            {
                break;
            }
            case 'POST':
            {
                if (isset($_POST['NAME']) && isset($_POST['DATA']) && count($_POST) === 2)
                {
                    var_dump($_POST);
                    $filename = $_POST['NAME'];
                    $filedata = $_POST['DATA'];
                    file_put_contents($filename, $filedata);
                    response::successful(200, false, array("name" => $filename, "data" => $filedata));
                    exit;
                }
                break;
            }
            default:
            {
                response::client_error(405);
                break;
            }
        }


    }
    else response::server_error(500);


?>
