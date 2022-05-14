<?php

    require_once "response.php";
    require_once "sqlc.php";
    require_once "token.php";
    
    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET':
            {
                if (isset($_GET['REFS']) && count($_GET) === 1)
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect();
                    $ids = sqlc::sel_fileids($id_user);
                    $ids = $ids?$ids:array();
                    response::successful(200, false, array("file_ids" => $ids));
                    exit;
                }
                else if (isset($_GET['ACTION']) && isset($_GET['ID']) && count($_GET) === 2)
                {
                    sqlc::connect();
                    switch ($_GET['ACTION'])
                    {
                        case 'PREVIEW': default: {
                            $d = sqlc::sel_file($_GET['ID']);
                            response::successful(200, false, array("name" => $d['fname']));
                            break;
                        }
                        case 'CONTENT': {
                            session_start();
                            $d = sqlc::sel_file($_GET['ID']);
                            sqlc::ins_tsf_data("d", $_SESSION['ID_USER'], $_SESSION['SESSION_SC_ID'], $_GET['ID']);
                            $ctx = file_get_contents($d['ref']);
                            response::successful(200, false, array("ctx" => $ctx, "name" => $d['fname']));
                            break;
                        }
                    }
                    exit;
                }
                else if (isset($_GET['OTPSTATE']))
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect();
                    $val = intval(sqlc::get_2fa($id_user));
                    response::successful(200, false, array("2FA" => $val));
                    exit;
                }
                else if (isset($_GET['TRANSFERS']))
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect();
                    $tsf = sqlc::get_tsf_table($id_user);
                    response::successful(200, false, array("TSF" => $tsf));
                    exit;
                }
                else response::client_error(400);

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

                        $ref = "../users/{$dir}/{$filename}";

                        file_put_contents($ref, $filedata);

                        $id_session_sc = $_SESSION['SESSION_SC_ID'];

                        $id_file = new token(16,"","",array("A-Z", "a-z", "0-9"));
                        $id_file = $id_file->val();

                        sqlc::ins_tsf_data("u", $id, $id_session_sc, $id_file);
                        sqlc::ins_file_data($id_file, $filename, $ref, $size, $id);
                        
                        response::successful(201);
                        exit;
                    }
                    else
                    {
                        // file alterato
                        response::server_error(500);
                    }
                }
                else if (isset($_POST['OTP']) && count($_POST) === 1)
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    $val = $_POST['OTP'];
                    sqlc::connect();
                    sqlc::set_2fa($id_user, intval($val));
                    $msg = $val ? "2FA activated" : "2FA disabled"; 
                    response::successful(201, $msg);

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
