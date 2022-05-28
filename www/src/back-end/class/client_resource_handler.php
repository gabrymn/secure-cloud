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
                    sqlc::connect("USER_STD_SEL");
                    $ids = sqlc::sel_fileids($id_user);
                    sqlc::close();
                    $ids = $ids?$ids:array();
                    response::successful(200, false, array("file_ids" => $ids));
                    exit;
                }
                else if (isset($_GET['ACTION']) && isset($_GET['ID']))
                {   

                    switch ($_GET['ACTION'])
                    {
                        case 'PREVIEW': default: {
                            sqlc::connect("USER_STD_SEL");
                            $d = sqlc::sel_file($_GET['ID']);
                            sqlc::close();
                            response::successful(200, false, array("name" => $d['nam'], "size" => $d['siz'], "mme" => $d['mme'], "upldate" => $d['dat']));
                            break;
                        }
                        case 'CONTENT': {
                            session_start();
                            sqlc::connect("USER_STD_SEL");
                            $d = sqlc::sel_file($_GET['ID']);
                            sqlc::close();                            
                            if (isset($_GET['DOWNLOAD'])){
                                sqlc::connect("USER_STD_INS");
                                sqlc::ins_tsf_data("d", $_SESSION['ID_USER'], $_SESSION['SESSION_SC_ID'], $_GET['ID']);
                                sqlc::close();
                            }
                            $ctx = file_get_contents($d['ref']);
                            
                            $ar = array("ctx" => $ctx, "name" => $d['nam']);
                            
                            if (isset($_GET['MIME']) && $_GET['MIME'])
                            {
                                $ar['mime'] = $d['mme'];
                            }

                            response::successful(200, false, $ar);
                            break;
                        }
                    }
                    exit;
                }
                else if (isset($_GET['OTPSTATE']))
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect("USER_STD_SEL");
                    $val = intval(sqlc::get_2fa($id_user));
                    sqlc::close();
                    response::successful(200, false, array("2FA" => $val));
                    exit;
                }
                else if (isset($_GET['TRANSFERS']))
                {
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect("USER_STD_SEL");
                    $tsf = sqlc::get_tsf_table($id_user);
                    sqlc::close();
                    response::successful(200, false, array("TSF" => $tsf));
                    exit;
                }
                else response::client_error(400);

                break;
            }
            case 'POST':
            {
                if (isset($_POST['NAM']) && isset($_POST['CTX']) && isset($_POST['SIZ']) && isset($_POST['IMP']) && isset($_POST['MME']) && count($_POST) === 5)
                {
                    // upload file
                    $filename = $_POST['NAM'];
                    $filedata = $_POST['CTX'];
                    $client_hash = $_POST['IMP'];
                    $mime = $_POST['MME'];
                    $server_hash = hash("sha256", $filename.$filedata);

                    if ($client_hash === $server_hash)
                    {
                        session_start();
                        
                        sqlc::connect("USER_STD_SEL");
                        $id_user = $_SESSION['ID_USER'];
                        $email = sqlc::get_email($id_user);
                        sqlc::close();

                        $size = $_POST['SIZ'];
                        
                        $dir = md5("dir" . $id_user . $email);

                        $ref = "../users/{$dir}/{$filename}";

                        $id_session_sc = $_SESSION['SESSION_SC_ID'];

                        $id_file = new token(16,"","",array("A-Z", "a-z", "0-9"));
                        $id_file = $id_file->val();

                        sqlc::connect("USER_STD_INS");
                        
                        // TRANSAZIONE
                        {
                            sqlc::ins_file_data($id_file, $filename, $ref, $size, $id_user, $mime);
                            file_put_contents($ref, $filedata);

                            // Se inserimento informazioni del file nel db non va a buon fine
                            //  => ROLLBACK in php
                            // Se inserimento file fisico non va a buon fine
                            //  => ROLLBACK in sql
                        }

                        sqlc::ins_tsf_data("u", $id_user, $id_session_sc, $id_file);

                        sqlc::close();
                        
                        response::successful(201, false, array("id" => $id_file));
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
                    sqlc::connect("USER_STD_UPD");
                    sqlc::set_2fa($id_user, intval($val));
                    sqlc::close();
                    $msg = $val ? "2FA activated" : "2FA disabled"; 
                    response::successful(201, $msg);

                    exit;            
                }

                break;
            }
            case 'DELETE': {
                $id = $_REQUEST['id'];
                
                sqlc::connect("USER_STD_DEL");
                sqlc::del_file($id);
                sqlc::close();

                sqlc::connect("USER_STD_SEL");
                $ref = sqlc::sel_ref($id);
                sqlc::close();
                unlink($ref);
                echo "File deleted";
                exit;
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