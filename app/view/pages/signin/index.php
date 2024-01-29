<?php

    define('__ROOT__', '../../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once 'script.php';

    $success = "";
    $error = "";
    $redirect = "";

    main($success, $error, $redirect);

    function main(&$success, &$error, &$redirect)
    {
        if (isset($_SERVER['REQUEST_METHOD']))
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET': {
                    handle_req($success, $error, $redirect);
                    break;
                }
    
                default: {
                    http_response::client_error(405);
                }
            }
        }
        else
        {
            http_response::server_error(500);
        }
    }

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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="../public/">HOME</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" style="font-weight:900;color:white" href="signin.php">Sign in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" style="color:white" href="signup.php">Sign up</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <br>
        <main class="login-form">
            <div class="cotainer">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <?php
                            if (isset($error) && $error != ""){
                                echo '<div class="alert alert-danger" onclick="this.remove()" role="alert">'.$error.'</div>';
                            }  
                        ?>  
                        <?php
                            if (isset($success) && $success != "")
                                echo '<div class="alert alert-success" onclick="this.remove()" role="alert">'.$success.'</div>';
                        ?>

                        <div id="login_error" style="display:none" class="alert alert-danger" onclick="this.style.display='none'" role="alert"></div>

                        <div class="card">
                            <div class="card-header">Sign in</div>
                            <div class="card-body">
                                <form id="signin_form">
                                    <div class="form-group row">
                                        <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input name="email" type="email" id="email" class="form-control" placeholder="user@example.com" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                        <div class="col-md-6">
                                            <input name="pwd" type="password" id="pwd" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                        <a href="password-reset/" class="btn btn-link">
                                            Forgot Your Password?
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </main>
                                
        <?php 
            // change the url
            // FROM localhost/signin?tkn=[EXPIRED_OR_INVALID_TOKEN]    
            // TO   localhost/signin
            if ($redirect !== "")
            {
                echo "<script>window.history.pushState('', '', 'signin.php');</script>";
            }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    </body>
</html>

<script>

    $('#signin_form').on('submit', async (e) => {
    
        e.preventDefault();

        var formData = new FormData(document.getElementById('signin_form'));

        const url = 'http://localhost/api/signin.php';
        const method = 'POST';

        try {
                const response = await fetch(url, 
                {
                    method: method,
                    body: formData,
                });

                if (response.ok)
                {
                    // test
                    //console.log(await response.text());
                    //return false;
                    
                    const json = await response.json();
                    window.location.href = json.redirect;
                }
                else
                {
                    const errorTxt = await response.text();
                    const errorJson = JSON.parse(errorTxt);
                    
                    if (errorJson.redirect !== undefined)
                        window.location.href = errorJson.redirect;
                    else
                    {
                        $('#login_error').css("display", "block");
                        $('#login_error').html(errorJson.status_message);
                    }
                }

        } catch (error) {
            console.log(error)
            $('#login_error').css("display", "block");
            $('#login_error').html("There was a problem, try again");
        }

        e.preventDefault();
    })


</script>


