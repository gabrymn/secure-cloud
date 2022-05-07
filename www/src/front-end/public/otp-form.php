<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/response.php';
    require_once __BACKEND__ . 'class/system.php';

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){
            case 'POST': {
                if (isset($_POST['OTP']) && count($_POST) === 1){
                    session_start();
                    
                    if (isset($_SESSION['AUTH']) && !isset($_SESSION['HOTP'])){
                        header("Location: pvt.php");
                        exit;
                    }

                    $hotp = hash("sha256", $_POST['OTP']);
                    $exp = $_SESSION['HOTP']['exp'];

                    if (time() > $exp)
                    {
                        unset($_SESSION['HOTP']);
                        header("Location: log.php");
                        exit;
                    }
                    if ($hotp === $_SESSION['HOTP']['value'])
                    {
                        unset($_SESSION['HOTP']);
                        $_SESSION['AUTH'] = 2;
                        header("Location: pvt.php");
                        exit;
                    }
                    else
                    {
                        response::print(400, $error, "Invalid code.");
                    }
                }
                break;
            }
            case 'GET': {
                if (isset($_COOKIE['PHPSESSID'])){
                    session_start();
                    if (isset($_SESSION['AUTH']) && !isset($_SESSION['HOTP'])){
                        header("Location: pvt.php");
                        exit;
                    }
                    else if (isset($_SESSION['HOTP']) && isset($_SESSION['ID_USER'])){
                        $exp = $_SESSION['HOTP']['exp'];
                        if (time() > $exp){
                            unset($_SESSION['HOTP']);
                            header("Location: log.php");
                            exit;
                        }
                    }
                    else 
                    {
                        header("Location: log.php");
                        exit;
                    }
                }else 
                {
                    header("Location: log.php");
                    exit;
                }
                break;
            }

            default: {
                response::client_error(405);
                break;
            }
        }

    }
    else response::server_error();
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>
        <main class="login-form">
            <div class="cotainer">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <?php
                            if (isset($error) && $error != "")
                                echo '<div class="alert alert-danger" onclick="this.remove()" role="alert">'.$error.'</div>';
                            unset($error);    
                        ?>
                        <div class="card">
                            <div class="card-header">OTP</div>
                            <div class="card-body">
                                <form id="OTP_FORM" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">6 char code</label>
                                        <div class="col-md-6">
                                            <input name="OTP" id="OTP_INPUT" class="form-control" maxlength="6" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>

    <script>

        $("#OTP_INPUT").on('keyup', () => {
            if ($("#OTP_INPUT").val().length === 6){
                $('#OTP_FORM').submit()
            }
        })


    </script>

</html>
