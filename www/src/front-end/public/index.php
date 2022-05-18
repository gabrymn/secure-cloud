<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/system.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';

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
                            header("Location: pvt.php");
                            exit;
                        }
                        if ($_SESSION['AUTH'] === 1)
                        {
                            if (!isset($_SESSION['HOTP']))
                            {
                                header("Location: pvt.php");
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
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">end-to-end</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="log.php">Sign in</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="reg.php">Sign up</a>
                </li>
            </ul>
            </div>
        </div>
        </nav>

        <div id="ID_COOKIE_BOX" class="divCenter" style="display:none">
            <div class="d-flex align-items-center align-self-center card p-3 text-center cookies"><img src="../img/COOKIE_IMG.png" width="50"><span class="mt-2">Utilizziamo i cookie per offrirti la miglior esperienza possibile sul nostro sito Web.</span>
                <button id="ID_COOKIE_A" class="btn btn-dark mt-3 px-4" type="button">Accetta</button>
                <button id="ID_COOKIE_R" class="btn btn-dark mt-3 px-4" type="button">Rifiuta</button>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>


<script type="module">

    import Cookie from "../class/cookie.js"

    $('document').ready(() => {
        //Cookie.Req()
        $('#ID_COOKIE_BOX').css("display", "block")
    })

    $('#ID_COOKIE_A').on('click', () => {
        Cookie.Set("allow__", "true", 2)
        $('#ID_COOKIE_BOX').css("display", "none")
    })

    $('#ID_COOKIE_R').on('click', () => {
        Cookie.Set("allow__", "false", 2)
        $('#ID_COOKIE_BOX').css("display", "none")
    })



</script>


<style>

    .divCenter {
        margin-left: auto;
        margin-right: auto;
        margin-top: 30px;
    }

    .card {
        width: 350px;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid #d2d2dc;
        border-radius: 6px;
        -webkit-box-shadow: 0px 0px 5px 0px rgb(249, 249, 250);
        -moz-box-shadow: 0px 0px 5px 0px rgba(212, 182, 212, 1);
        box-shadow: 0px 0px 5px 0px rgb(161, 163, 164);
    }

    .cookies a {
        text-decoration: none;
        color: #000;
        margin-top: 8px;
    }

    .cookies a:hover {
        text-decoration: none;
        color: blue;
        margin-top: 8px;
    }


</style>