<?php

    require_once "response.php";
    require_once "sqlc.php";

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
                if (isset($_POST['NAM']) && isset($_POST['CTX']) && isset($_POST['SIZ']) && isset($_POST['IMP']) && count($_POST) === 4)
                {
                    // upload file
                    $filename = $_POST['NAM'];
                    $filedata = $_POST['CTX'];
                    $client_hash = $_POST['IMP'];
                    $server_hash = hash("sha256", $filename.$filedata);

                    if ($client_hash === $server_hash)
                    {
                        session_start();
                        sqlc::connect();
    
                        $id = $_SESSION['ID_USER'];
                        $email = sqlc::get_email($id);
                        $size = $_POST['SIZ'];
                        
                        $dir = md5("dir" . $id . $email);

                        file_put_contents("../users/{$dir}/{$filename}", $filedata);
                        //sqlc::upl_file($server_hash, $id, $size);
                        
                        response::successful(201, false, array("filename" => $filename, "filedata" => $filedata));
                        exit;
                    }
                    else
                    {
                        // file alterato
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
