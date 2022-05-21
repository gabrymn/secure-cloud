<?php

    require_once '../../../back-end/class/response.php';
    require_once '../../../back-end/class/sqlc.php';
    require_once '../../../back-end/class/token.php';
    require_once '../../../back-end/class/email.php';
    require_once '../../../back-end/class/dmn.php';

    if (!defined('DMN')) define('DMN', get_dmn()); 

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD']))
    {

        switch ($_SERVER['REQUEST_METHOD'])
        {

            case 'POST': {

                if (isset($_REQUEST['EMAIL']) && count($_POST) === 1)
                {
                    
                    $tkn = new token(150);
                    $htkn = hash('sha256', $tkn->val());

                    sqlc::connect();
                    $id = sqlc::get_id_user($_REQUEST['EMAIL']);

                    if ($id === 0)
                    {
                        $html_ctx = file_get_contents("forms/0")."<script>$('#ERROR').css('display','block');$('#ERROR').html('Invalid email.')</script>";
                        goto front_end;
                    }

                    if (sqlc::rec_account($htkn, $id) === 0)
                    {
                        response::print(400, $error, "Internal server error. Try again.");
                        goto front_end;
                    }

                    $subject = 'RESET YOUR PASSWORD';
                    $ltkn = $tkn->val();
                    $message = "click ".DMN."/secure-cloud/www/src/front-end/public/password-reset/?TKN=$ltkn for reset your password";

                    send_email($_REQUEST['EMAIL'], $subject, $message);
                    $html_ctx = file_get_contents("forms/1");

                    unset($subject);
                    unset($message);
                    unset($htkn);
                    unset($id);

                }
                else if (isset($_REQUEST['NEW_PASSWORD']) && isset($_REQUEST['EMAIL']) && isset($_REQUEST['HTKN']))
                {
                    
                    // [change passowrd](1): OK
                    // [turn back] <-
                    // [change password](2): error, !EXPIRED URL!
                    {
                        sqlc::connect();
                        if (sqlc::get_tkn_row($_REQUEST['HTKN']) === 0)
                        {
                            $html_ctx = file_get_contents("forms/0")."<script>$('#ERROR').css('display','block');$('#ERROR').html('Invalid or expired password reset link.')</script>";
                            $change_url = "password_reset.php"; 
                            goto front_end;
                        }
                    }

                    $hpass = password_hash($_REQUEST['NEW_PASSWORD'], PASSWORD_BCRYPT);

                    sqlc::connect();
                    sqlc::del_tkn($_REQUEST['HTKN']);
                    
                    if (sqlc::ch_pass($_REQUEST['EMAIL'], $hpass) === 0){
                        response::server_error(500);
                    }

                    $html_ctx = file_get_contents("forms/3");

                }
                else
                {
                    response::client_error(400, "Bad parameters");
                }

                break;
            }

            case 'GET': {
                
                if (count($_GET) === 0)
                {
                    $html_ctx = file_get_contents("forms/0");
                }

                else if (isset($_REQUEST['TKN']))
                {

                    $tkn = $_REQUEST['TKN'];

                    // Se length != da 150 inutile controllare nel DB, il token non è valido
                    if (strlen($tkn) !== 150)
                    {
                        $html_ctx = file_get_contents("forms/0")."<script>$('#ERROR').css('display','block');$('#ERROR').html('Invalid or expired password reset link.')</script>";
                        $change_url = "password_reset.php";
                        goto front_end;
                    }

                    $htkn = hash("sha256", $tkn);
                    sqlc::connect();
                    $data = sqlc::get_tkn_row($htkn);
                    
                    if ($data === 0)
                    {
                        $html_ctx = file_get_contents("forms/0")."<script>$('#ERROR').css('display','block');$('#ERROR').html('Invalid or expired password reset link.')</script>";
                        $change_url = "password_reset.php";
                        goto front_end;
                    }
                    
                    $js = "<script>var input = $('<input>').attr('type', 'hidden').attr('name','EMAIL').val('".$data['email']."');$('#FORM_ID_2').append($(input));var input1 = $('<input>').attr('type', 'hidden').attr('name','HTKN').val('".$htkn."');$('#FORM_ID_2').append($(input1));</script>";
                    
                    $html_ctx = str_replace("TEMPLATE", $data['email'], file_get_contents("forms/2")) . $js;

                    unset($tkn);
                    unset($htkn);
                    unset($data);
                    unset($js);
                }

                break;
            }
            
            default: {
                response::client_error(405, "Metodo non ammesso");
                break;
            }
        }
    }
    else response::server_error(500);

    front_end:
?>

<!------ START BOOTSTRAP FORM ---------->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="../../css/shared.css">
        <link rel="stylesheet" href="../../css/login.css">
        <link href="../../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../public/">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                </li>
                <li class="nav-item">
                <a class="nav-link" style="color:white" href="../signin.php">Sign in</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" style="color:white" href="../signup.php">Sign up</a>
                </li>
            </ul>
            </div>
        </div>
        </nav>
        <br>
        <?php
            if (isset($html_ctx))
            { 
                response::html_ctx($html_ctx); 
                unset($html_ctx); 
            }
            else
            {
                echo "bad request";
            }
        ?>

        <?php 
            if (isset($change_url))
            {
                echo "<script>window.history.pushState('', '', '".$change_url."');</script>";
                unset($change_url);
            }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../../js/shared.js"></script>
    </body>
</html>
