<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/system.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'GET': {

                session_start();

                if (!isset($_SESSION['VERIFING_EMAIL'])){
                    header("Location: log.php");
                    exit;
                }

                if (isset($_GET['first']) && count($_GET) === 1)
                {

                    $t = "Verifica email";

                    if ($_GET['first'] == 1)
                    {
                        $p = "Ti abbiamo inviato un email di verifica, controlla la tua casella di posta";
                        $b = "Clicca per continuare";
                        $r = 'log.php';
                    }
                    else
                    {   
                        $p = "Verifica il tuo account prima di poter accedere. <br><br>Non hai ricevuto la nostra mail?";
                        $b = "Clicca qui";
                        $r = 'verify.php?';
                    }
                }
                else
                {
                    if (isset($_COOKIE['PHPSESSID']))
                    {
                        session_start();
                        $email = $_SESSION['EMAIL'];
                        system::send_email_verification($email, "verify.php?first=1");
                    }
                    else header("Location: log.php");
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
                        <div class="card">
                            <div class="card-header"><?php echo $t; ?></div>
                            <div class="card-body">
                                <form action='<?php echo $r; ?>'>
                                    <div class="form-group row">
                                        <div class="col-md-6 offset-md-4">
                                        <p>
                                            <?php echo $p; ?>
                                        </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            <?php echo $b; ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>

        </body>
    </html>