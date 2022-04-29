<?php

    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID'])){
                    session_start();
                    if (isset($_SESSION['ID_USER'])){
                        system::redirect_priv_area($_SESSION['ID_USER']);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="">HOME</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Crittografia</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="log.php">Accedi</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="reg.php">Registrati</a>
            </li>
        </ul>
        </div>
    </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>

<style>
    
    @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap');

    * {
        font-family: 'Quicksand', sans-serif;
    }

    body {
        background-color: rgb(60,60,60);
    }

    ::selection {
        background-color: rgb(150,150,150);
    }

</style>

<script>    

    /*$('document').ready(() => {
        if (getCookie('ALLOW') === ''){
            let text = 'Accetti i cookie?';
            if (confirm(text) === true) 
                setCookie('ALLOW', '1');
        }
    });*/

</script>