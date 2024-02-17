<?php

    require_once 'script.php';
    
    require_once __ROOT__ . 'model/ds/crypto.php';
    require_once __ROOT__ . 'model/ds/client.php';
    require_once __ROOT__ . 'model/models/session.php';
    require_once __ROOT__ . 'model/ds/token.php';

    $data = main(); 
    $email = $data[0];
    $secret_2fa_c = $data[1];
    $rkey_c = $data[2];
    $ckey_c = $data[3];

    $dkey = $_SESSION['DKEY'];
    echo $dkey;
    exit;

    /*$s = new Session(ip: client::get_ip(), id_user: $_SESSION['ID_USER']);

    echo "<h1>Session ID:</h1>";
    var_dump($s->sel_idsession_active_from_iduser_ipclient());

    $rkey = crypto::decrypt_AES_GCM($rkey_c, $_SESSION['DKEY']);
    
    $ckey = crypto::decrypt_AES_GCM($ckey_c, $rkey);

    $secret_2fa = crypto::decrypt_AES_GCM($secret_2fa_c, $rkey);

    $tfa = new MyTFA($email, $secret_2fa);*/

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
    <script src="../../js/url.js"></script>
    <script src="script.js"></script>
</body>
</html>

<script>
    const DOMAIN = '<?php echo $_ENV['DOMAIN']; ?>';
    const RECOVERY_KEY = '<?php echo $rkey; ?>';
    const RECOVERY_KEY_FILENAME = "SC-RECOVERYKEY.txt";
</script>
