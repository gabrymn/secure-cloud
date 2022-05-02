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
                if (isset($_POST['NAME']) && isset($_POST['DATA']) && isset($_POST['H']) && count($_POST) === 3)
                {
                    $filename = $_POST['NAME'];
                    $filedata = $_POST['DATA'];

                    $client_hash = $_POST['H'];
                    $server_hash = hash("sha256", $filename.$filedata);

                    if ($client_hash === $server_hash)
                    {
                        file_put_contents($filename, $filedata);
                        response::successful(200, false, array("name" => $filename, "data" => $filedata));
                        exit;
                    }
                    else
                    {
                        // documento digitale manomesso
                        response::server_error(500);
                    }
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
