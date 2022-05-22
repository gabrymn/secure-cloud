<?php

    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET': {

                if (isset($_COOKIE['PHPSESSID']))
                {
                    session_start();
                    if (isset($_SESSION['AUTH']))
                    {
                        if ($_SESSION['AUTH'] === 2)
                        {
                            header("Location: ../private/cloud.php");
                            exit;
                        }
                        if ($_SESSION['AUTH'] === 1)
                        {
                            if (!isset($_SESSION['HOTP']))
                            {
                                header("Location: ../private/cloud.php");
                                exit;
                            }
                        }
                    }
                }

                if (isset($_COOKIE['rm_tkn']))
                {
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }

                break;
            }
            default: {
                response::client_error(405);
                break;
            }
        }
    }    
    else
    {
        response::server_error(500);
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
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link href="../css/shared.css" rel="stylesheet">
        <link href="../css/cookie.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid #157EFB">
        <div class="container-fluid">
            <a class="navbar-brand" style="font-weight:900" href="">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" style="color:white" href="signin.php">Sign in</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" style="color:white" href="signup.php">Sign up</a>
                </li>
            </ul>
            </div>
        </div>
        </nav>

        <br><br>
        <div class="jumbotron mainbox">

            <div id="photo" style="text-align: left">
                <span class="display-4" style="vertical-align:middle">Secure Cloud </span>
                <img style="vertical-align:middle" src="../img/l.png" width="auto" height="100px" alt="">
            </div>
            <br>
            <p class="lead">Spazio di archiviazione cloud e comunicazione sicura.</p>
            <p class="lead">I tuoi dati sono cifrati attraverso la crittografia AES.</p>
            <hr class="my-4">
            <p class="lead">
                <a class="btn btn-primary btn-lg" href="signup.php" role="button">Inizia</a>
            </p>
        </div>

        <div id="ID_COOKIE_BOX" class="row" style="display:none">
            <div class="col-md-4 col-sm-12 button-fixed">
            <div class="p-3 pb-4 bg-custom text-white">
            <div class="row">
            <div class="col-10">
            <h1>Allow Cookies</h1>
            </div>
            <div class="col-2 text-center">
            <i class="fas fa-times"></i>
            </div>
            </div>
            <p>Utilizziamo i cookie per migliorare la tua esperienza</p>
            <button id="ID_COOKIE_A" type="button" class="btn btn-light w-100">Accept</button>
            </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>


<script type="module">

    import Cookie from "../class/cookie.js"

    $('document').ready(() => {
        if (Cookie.Get("allow__") === null)
            $('#ID_COOKIE_BOX').css("display", "block")
    })

    $('#ID_COOKIE_A').on('click', () => {
        Cookie.Set("allow__", "true", 30)
        $('#ID_COOKIE_BOX').css("display", "none")
    })


</script>

<style>

    .mainbox {

        width: 90%;
        margin-left: auto;
        margin-right: auto;
    }

</style>