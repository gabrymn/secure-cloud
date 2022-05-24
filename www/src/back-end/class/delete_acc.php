<?php

    require_once "response.php";
    require_once "sqlc.php";

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'DELETE':
            {
                session_start();
                $id_user = $_SESSION['ID_USER'];
                sqlc::connect("USER_STD_DEL");
                sqlc::del_account($id_user);
                sqlc::close();

                sqlc::connect("USER_STD_SEL");
                $email = sqlc::get_email($id_user);
                sqlc::close();
                $dir_user = md5("dir" . $id_user . $email);
                rmdir_force("../users/$dir_user");
                echo 1;
                exit;
            }
            default:
            {
                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);


    function rmdir_force(string $directory): bool
    {
        array_map(fn (string $file) => is_dir($file) ? rmdir_force($file) : unlink($file), glob($directory . '/' . '*'));
    
        return rmdir($directory);
    }

?>