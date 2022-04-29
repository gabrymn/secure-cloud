<?php

    require '../resources/api.php';

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
</head>
<body>
    <br>
    <center><div style="border:2px solid black;border-radius:25px;width:60%">
        <h1>HOME PAGE</h1>
        <h2><a href="login">Login</a><h2>
        <h2><a href="signup">Signup</a><h2>
    </div></center>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://www.mywebs.altervista.org/Final/resources/api.js"></script>
</body>
</html>

<script>    

    $('document').ready(() => {
        if (getCookie('ALLOW') === ''){
            let text = 'Accetti i cookie?';
            if (confirm(text) === true) 
                setCookie('ALLOW', '1');
        }
    });

</script>