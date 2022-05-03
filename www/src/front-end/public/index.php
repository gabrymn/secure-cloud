<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/system.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID'])){
                    session_start();
                    if (isset($_SESSION['AUTH'])){
                        //system::redirect_priv_area($_SESSION['ID_USER']);
                        header("Location: pvt.php");
                        exit;
                    }
                }

                if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
                    if ($_COOKIE['logged']){
                        $tkn = $_COOKIE['rm_tkn'];
                        $htkn = hash("sha256", $tkn);
                        sqlc::connect();
                        $data = sqlc::rem_sel($htkn); // controlla se il token è valido
                        if ($data) system::redirect_priv_area($data['id_user']);
                    }
                }

                break;
            }
            default: {
                response::client_error(405);
                break;
            }
        }
    }    
    else{
        response::server_error(500);
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
    <title>Home page</title>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>