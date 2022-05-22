<?php   

    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    
    $success = "";
    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {
                if (isset($_REQUEST['EMAIL']) && isset($_REQUEST['PASS'])){

                    if (filter_var($_REQUEST['EMAIL'], FILTER_VALIDATE_EMAIL)){

                        sqlc::connect("USER_STD_SEL");

                        if (sqlc::login($_REQUEST['EMAIL'], $_REQUEST['PASS'])){

                            $id_user = sqlc::get_id_user($_REQUEST['EMAIL']);

                            if (sqlc::user_verified($id_user) === 0){
                                session_start();
                                $_SESSION['EMAIL'] = $_REQUEST['EMAIL'];
                                $_SESSION['VERIFING_EMAIL'] = 1;
                                system::verify($_REQUEST['EMAIL'], 0);
                                exit;
                            }

                            session_start();
                            $_SESSION['ID_USER'] = $id_user;
                            $_SESSION['AUTH'] = 1;

                            if (isset($_REQUEST['REM_ME']) && isset($_COOKIE['allow__']))
                            {
                                if ($_REQUEST['REM_ME'] && $_COOKIE['allow__'] === "true")
                                {
                                    system::remember($id_user); 
                                    unset($_REQUEST['REM_ME']);
                                }
                            }

                            unset($_REQUEST['EMAIL']);
                            unset($_REQUEST['PASS']);
                            
                            sqlc::close();
                            sqlc::connect("USER_STD_SEL");
                            $status_2FA = sqlc::get_2fa($id_user);
                            sqlc::close();
                            if ($status_2FA)
                                system::redirect_otp_form($id_user);
                            else
                                header("Location: ../private/cloud.php");
    
                            sqlc::close();
                            exit;

                        }else { 
                            response::print(400, $error, "Incorrect email or password."); 
                        }

                    }else{ 
                        response::print(400, $error, "Incorrect email."); 
                    }

                }else { 
                    response::client_error(404); 
                }
            }

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

                    if (isset($_SESSION['HOTP']) && isset($_SESSION['ID_USER']))
                    {
                        $exp = $_SESSION['HOTP']['exp'];
                        if (time() > $exp)
                        {
                            unset($_SESSION['HOTP']);
                            exit;
                        }
                    }
                }

                if (isset($_COOKIE['rm_tkn']))
                {
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }

                if (isset($_GET['tkn']))
                {
                    sqlc::connect("USER_STD_SEL");
                    $id_user = sqlc::get_tkn_verify(hash("sha256", $_GET['tkn']));
                    sqlc::close();
                    if ($id_user)
                    {
                        if (!session_status()) session_start();
                        if (isset($_SESSION['VERIFING_EMAIL']))
                        {
                            unset($_SESSION['VERIFING_EMAIL']);
                        }
                        sqlc::connect("USER_STD_UPD");
                        sqlc::upd_verified(intval($id_user));
                        sqlc::close();
                        response::print(200, $success, "Hai verificato la tua email, accedi.");
                        sqlc::connect("USER_STD_DEL");
                        sqlc::del_tkn_verify(intval($id_user));
                        sqlc::close();
                    }
                    else
                    {
                        response::print(400, $error, "Invalid or expired email verify link.");
                    }

                    $change_url = "signin.php";
                }

                break;
            }

            default: {

                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);

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
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link rel="stylesheet" href="../css/cookie.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
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
                                unset($error);  
                            }  
                        ?>  
                        <?php
                            if (isset($success) && $success != "")
                                echo '<div class="alert alert-success" onclick="this.remove()" role="alert">'.$success.'</div>';
                            unset($success);    
                        ?>
                        <div class="card">
                            <div class="card-header">Sign in</div>
                            <div class="card-body">
                                <form id="FRM_LGN" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <div class="form-group row">
                                        <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input name="EMAIL" type="email" id="EML" class="form-control" placeholder="user@example.com" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                        <div class="col-md-6">
                                            <input name="PASS" type="password" id="PSW" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6 offset-md-4">
                                            <div class="checkbox">
                                                <label>
                                                    <input name="REM_ME" type="checkbox" id="REM_ME"> <label for="REM_ME">Remember me</label>
                                                </label>
                                            </div>
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
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </main>
                                
        <?php 
            if (isset($change_url))
            {
                echo "<script>window.history.pushState('', '', '".$change_url."');</script>";
                unset($change_url);
            }
        ?>

        <div id="ID_COOKIE_BOX" class="row" style="display:none;">
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>

    <script type="module">

        import uid from '../class/unique.js';
        import cryptolib from '../class/cryptolib.js';
        import Cookie from '../class/cookie.js';
            
        const SHA256 = cryptolib['HASH'].SHA256;

        $('document').ready(() => {
            if (Cookie.Get("allow__") === null)
                $('#ID_COOKIE_BOX').css("display", "block")
        })

        $('#ID_COOKIE_A').on('click', () => {
            Cookie.Set("allow__", "true", 1)
            $('#ID_COOKIE_BOX').css("display", "none")
        })

        $('#FRM_LGN').on('submit', () => {
            const email = $('#EML').val();
            const psw = $('#PSW').val();
            const k = SHA256(email + psw);
            localStorage.setItem("k", k);
        });

    </script>

</html>




