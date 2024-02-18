<?php

    require_once __DIR__ . '/../../resource/crypto.php';
    
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    $dkey = $_SESSION['DKEY'];
    $rkey_c = "LR8nferUfj79GoEoKQbWiIdJFRBplZrHTGfBtCTLwH16Md/h1lzaYzGBNhGeeqsP5b4N0WBa+j1ilKRQmacd1kmjSIC6ETZoL9pF1g==";
    $rkey = crypto::decrypt_AES_GCM($rkey_c, $dkey);
    echo $rkey;
    
    $rkey_app = "VTQ9WlCahhbnvt5yCwKoFpBKjIp3TbnBkxkZk42EjCM=";

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

    <img src='<?php echo $tfa->get_qrcode_url(); ?>' alt="QR Code">

    <br>
    
    <br><br><br>

    <button id="rkeyDownloadButton">Esporta chiave di recupero</button>

    <br>
    <br>
    <br>

    <button onclick='logoutCall()'><h1>LOGOUT</h1></button>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/url.js"></script>
    <script src="JS/pages/clouddrive.js"></script>
</body>
</html>

<script>
    const DOMAIN = '<?php echo $_ENV['DOMAIN']; ?>';
    const RECOVERY_KEY = '<?php echo $rkey; ?>';
    const RECOVERY_KEY_FILENAME = "SC-RECOVERYKEY.txt";
</script>
