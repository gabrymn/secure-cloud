<?php

    define('__ROOT__', '../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . '/model/http/http_response.php';
    require_once __ROOT__ . '/model/google2FA.php';
    require_once __ROOT__ . '/model/qry.php';
    require_once __ROOT__ . '/model/mypdo.php';
    
    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET': {
                
                session_start();

                if (!isset($_SESSION['LOGGED']) || !isset($_SESSION['ID_USER']))
                {
                    http_response::client_error(401);
                }
                else
                {
                    $id_user = $_SESSION['ID_USER'];

                    $conn = MYPDO::get_new_connection('USER_TYPE_SELECT', $_ENV['USER_TYPE_SELECT']);
                    $secret_2fa = QRY::sel_secret_2fa_from_id($conn, $id_user, __QP__);
                    $email = QRY::sel_email_from_id($conn, $id_user, __QP__);
                    MYPDO::close_connection($conn);

                    $g = new Google2FA($email, $secret_2fa);
                }

                break;
            }

            default: {
                http_response::client_error(405);
            }
        }
    }
    else http_response::server_error(500);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Page</title>
</head>
<body>
    <h1>QR Code Example</h1>

    <p>Scansiona il QR code con la tua app Google Authenticator:</p>

    <img src="data:image/png;base64, <?php echo $g->get_qrcode_img(); ?>" alt="QR Code">

    <br>
    <?php echo $secret_2fa ?>

    <br>
    <br>
    <br>

    <button onclick='redirect()'><h1>LOGOUT</h1></button>

</body>
</html>

<script>
    const redirect = () => 
    {
        const URL = "http://localhost/api/logout.php";
        window.location.href = URL;
    }

</script>