<?php

    require_once 'script.php';

    $data = main();

    $g = $data[0];
    $secret_2fa = $data[1];
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

    <script src="../../js/url.js"></script>
</body>
</html>

<script>
    const redirect = () => 
    {
        const URL = "http://localhost/api/logout.php";
        window.location.href = URL;
    }

</script>