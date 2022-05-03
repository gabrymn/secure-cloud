<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';
    require_once __BACKEND__ . 'class/system.php';
    
	if (isset($_COOKIE['PHPSESSID']))
	{
		session_start();
        if (isset($_SESSION['ID_USER']) && isset($_SESSION['AUTH']))
        {
            sqlc::connect();
            $email = sqlc::get_email($_SESSION['ID_USER']);
            echo "<h1>Private area of: [ $email ]</h1><br>";
            echo "<h3><a href='../../back-end/class/out.php'>logout</a></h3>";
        }
        else
            response::client_error(403);
	}
    else if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
        if ($_COOKIE['logged']){
            $tkn = $_COOKIE['rm_tkn'];
            $htkn = hash("sha256", $tkn);
            sqlc::connect();
            $data = sqlc::rem_sel($htkn);
            if ($data) system::redirect_priv_area($data['id_user']);
        }
    }
	else
        response::client_error(403);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private area</title>
</head>
<body>
    <input id="ID_FILE_UPLOADER" type="file">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>

<script type="module">

    import CLIENT_FILE from '../class/clientfile.js';
    import cryptolib from '../class/cryptolib.js'

    $('document').ready(() => {
        checkKey();
    })

    const checkKey = () => {
        const k = localStorage.getItem("k");
        if (k === null){
            alert("Chiave mancante, e' necessario riaccedere");
            window.location.href = "../../back-end/class/out.php";
            return;
        };
    }

    $("#ID_FILE_UPLOADER").on('change', async (e) => {

        checkKey();

        // upload file
        const file = {
            inf: e.target.files[0],
            ctx: await CLIENT_FILE.TO_BASE64(e.target.files[0]).catch((error) => console.log("Error uploading your file."))
        }
        const fobj = new CLIENT_FILE(file.inf, file.ctx)
        const aes = new cryptolib['AES']("getItem");
        const hash = cryptolib['HASH'].SHA256;
        const [NAM, CTX, IMP, SIZ] = fobj.ENCRYPT(aes, hash);

        $.ajax({
            type: 'POST',
            url: "../../back-end/class/client_resource_handler.php",
            data: {NAM:NAM, CTX:CTX, IMP:IMP, SIZ:SIZ},
            success: (response) => {
                console.log(response);
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    });

</script>