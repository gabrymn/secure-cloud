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
                else if (isset($_GET['SESSION_ID']) && count($_GET) === 1)
                {
                    sqlc::connect();
                    $sstatus = sqlc::sel_session_status($_GET['SESSION_ID']);
                    response::light(200, $sstatus);
                }
                else response::client_error(400);

                break;
            }
            case 'POST':
            {
                if (isset($_POST['SESSION_ID']) && count($_POST) === 1)
                {
                    $session_id = $_POST['SESSION_ID'];
                    sqlc::connect();
                    sqlc::expire_session($session_id);
                    response::successful(200);
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