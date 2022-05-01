<?php

    require_once '../../../back-end/class/response.php';
    require_once '../../../back-end/class/sqlc.php';
    require_once '../../../back-end/class/token.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {

                if (isset($_REQUEST['EMAIL']) && count($_POST) === 1){
                    
                    $tkn = new token(150);
                    $htkn = hash('sha256', $tkn->val());

                    sqlc::connect();
                    $id = sqlc::get_id_user($_REQUEST['EMAIL']);

                    if ($id === 0){
                        response::client_error(400, "Email does not exists");
                    }

                    if (sqlc::rec_account($htkn, $id) === 0){
                        response::server_error(500);
                    }

                    $subject = 'RESET YOUR PASSWORD';
                    $ltkn = $tkn->val();
                    $message = "click http://127.0.0.1/secure-cloud/www/src/front-end/public/password-reset/password_reset.php?TKN=$ltkn for reset your password";

                    mail($_REQUEST['EMAIL'], $subject, $message);
                    $html_ctx = file_get_contents("forms/1.html");

                    unset($subject);unset($message);unset($htkn);unset($id);

                }else if (isset($_REQUEST['NEW_PASSWORD']) && isset($_REQUEST['EMAIL']) && isset($_REQUEST['HTKN'])){
                    
                    // [change passowrd](1): OK
                    // [turn back] <-
                    // [change password](2): error, !EXPIRED URL!
                    {
                        sqlc::connect();
                        if (sqlc::get_tkn_row($_REQUEST['HTKN']) === 0)
                        {
                            $html_ctx = invalid_or_expired_passwordreset_link('password_reset');
                            goto end_php;
                        }
                    }

                    $hpass = password_hash($_REQUEST['NEW_PASSWORD'], PASSWORD_BCRYPT);

                    sqlc::connect();
                    sqlc::del_tkn($_REQUEST['HTKN']);
                    
                    if (sqlc::ch_pass($_REQUEST['EMAIL'], $hpass) === 0){
                        response::server_error(500);
                    }

                    $html_ctx =
                    "<h1>password changed OK</h2><br>" .
                    "<h3><span>click <a href ='login'>here</a> for log in</span><h3>";

                }else{
                    response::client_error(400, "Bad parameters");
                }

                break;
            }

            case 'GET': {
                
                if (count($_GET) === 0){
                    $html_ctx = file_get_contents("forms/0.html");
                }

                else if (isset($_REQUEST['TKN'])){

                    $tkn = $_REQUEST['TKN'];

                    // Se length != da 150 inutile controllare nel DB, il token non è valido
                    if (strlen($tkn) !== 150){
                        $html_ctx = invalid_or_expired_passwordreset_link('password_reset.php');
                        goto end_php;
                    }

                    $htkn = hash("sha256", $tkn);
                    sqlc::connect();
                    $data = sqlc::get_tkn_row($htkn);
                    
                    if ($data === 0){
                        $html_ctx = invalid_or_expired_passwordreset_link('password_reset.php');
                        goto end_php;
                    }
                    
                    $js = "<script>var input = $('<input>').attr('type', 'hidden').attr('name','EMAIL').val('".$data['email']."');$('#FORM_ID_2').append($(input));var input1 = $('<input>').attr('type', 'hidden').attr('name','HTKN').val('".$htkn."');$('#FORM_ID_2').append($(input1));</script>";
                    
                    $html_ctx = "<h2>Change password for [".$data['email'] . "]</h2><br>" .
                    file_get_contents("forms/2.html") . $js;
                    unset($tkn);unset($htkn);unset($data);unset($js);
                }

                break;
            }
            
            default: {
                response::client_error(405, "Metodo non ammesso");
                break;
            }
        }
        end_php:
    }
    else response::server_error(500);

    function invalid_or_expired_passwordreset_link($redirect_uri){
        response::ctype('HTML');
        response::code(400);
        return "<h1 style='color:red'>INVALID OR EXPIRED PASSWORD RESET LINK</h1>" .
        "<span style='font-size:20px;'>CLICK <a href='{$redirect_uri}'>HERE</a> FOR RESET YOUR PASSWORD</span>";
    }

?>

<!------ START BOOTSTRAP FORM ---------->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="../../css/shared.css">
        <link rel="stylesheet" href="../../css/login.css">
        <title>Reset your password</title>
    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">end-to-end</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="../log.php">Sign in</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="../reg.php">Sign up</a>
                </li>
            </ul>
            </div>
        </div>
        </nav>

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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../../js/shared.js"></script>
    </body>
</html>