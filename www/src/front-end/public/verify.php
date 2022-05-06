<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/system.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'GET': {
                if (isset($_GET['first']) && count($_GET) === 1)
                {
                    if ($_GET['first'] == 1)
                    {
                        $form = 
                        "<h1>Ti abbiamo inviato un email di verifica</h1>".
                        "<h3>Controlla la tua casella di posta</h3>".
                        "<a href='log.php'>Clicca per continuare</a>";
                    }
                    else
                    {
                        $form = 
                        "<h1>Verifica il tuo account prima di poter accedere</h1>".
                        "<h3>Non hai ricevuto la nostra mail?</h3>".
                        "<a href='verify.php?send_email=1'>Clicca qui</a>";
                    }
                }
                else if (isset($_GET['send_email']) && count($_GET) === 1)
                {
                    session_start();
                    $email = $_SESSION['EMAIL'];
                    system::send_email_verification($email);
                }
                else if (isset($_GET['verified']) && count($_GET) === 1)
                {
                    $form = 
                    "<h1>Hai verifica l'account</h1>".
                    "<a href='log.php'>Clicca qui per accedere</a>";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify</title>
</head>
<body>

    <h1>TITOLO EMAIL VERIFICA</h1>
    <br>
    <?php echo $form; ?>

</body>
</html>