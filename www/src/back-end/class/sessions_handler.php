<?php

    require_once "response.php";
    require_once "sqlc.php";

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
            {
                if (isset($_GET['ID_USER']) && count($_GET) === 1)
                {
                    sqlc::connect();
                    $id_user = $_GET['ID_USER'];
                    $sessions = sqlc::sel_session_all(intval($_GET['ID_USER']));
                    response::successful(200, false, array("sessions" => $sessions));
                    exit;
                }
                else response::client_error(400);

                break;
            }
            case 'POST':
            {
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