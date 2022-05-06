<?php

    require_once 'backend-dir.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';
    require_once __BACKEND__ . 'class/system.php';

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID']))
                {
                    session_start();
                    if (isset($_SESSION['ID_USER']) && isset($_SESSION['AUTH']))
                    {
                        if (isset($_SESSION['HOTP']))
                        {
                            header("Location: otp-form.php");
                            exit;
                        }
                        sqlc::connect();
                        $email = sqlc::get_email($_SESSION['ID_USER']);
                    }
                    else header("Location: log.php");
                }
                else if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
                    if ($_COOKIE['logged']){
                        system::redirect_remember($_COOKIE['rm_tkn']);
                    }   
                }
                else header("Location: log.php");

                break;
            }

            case 'POST': {

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
    <link rel="stylesheet" href="../css/shared.css">
    <title>Private area</title>
    <link href="../img/icon.svg" rel="icon" type="image/x-icon" sizes="32x32">
</head>
<body>
    <h1>Private area of <?php echo $email; ?> </h1>
    <h3><a href='../../back-end/class/out.php'>logout</a></h3>
    <center><input type="file" id="ID_FILE_UPLOADER"></center><br>
    <div id="C_FILES" class="FILE_CARDS"></div>

    <br>
    <h3>2FA</h3><input id="OTP_YN" type="checkbox">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>

<script type="module">
    
    import CLIENT_FILE from '../class/clientfile.js'
    import cryptolib from '../class/cryptolib.js'
    import FILE_URL from '../class/blob.js'
    
    $('document').ready(() => {
        checkKey();
        sync_2FA_state();
        getData();
    })

    const getData = () => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {DATA:true},
            success: (response) => {
                console.log(response);
                const files = response.files;
                const rep = response.rep;
                var a = new cryptolib['AES'](localStorage.getItem("k"));
                response.files.forEach((filename) => {
                    if (filename !== "." && filename !== "..")
                        getFile(filename, rep, a)
                });
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }
    
    const getFile = (filename, rep, aes) => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {FILE:filename,REP:rep},
            success: (response) => {
                console.log(response);
                filename = filename.replaceAll("_", "/");
                var ctx = response.ctx;
                var [fn, url, blob] = GET_FILE_EXE(filename, ctx, aes);
                createVisualObj(url, fn);
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

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
        var aes = new cryptolib['AES'](localStorage.getItem("k"));
        const hash = cryptolib['HASH'].SHA256;
        const [NAM, CTX, IMP, SIZ] = fobj.ENCRYPT(aes, hash);
        var [fn, url, blob] = GET_FILE_EXE(NAM, CTX, aes);

        await createVisualObj(url, e.target.files[0].name);

        $.ajax({
            type: 'POST',
            url: "../../back-end/class/client_resource_handler.php",
            data: {NAM:NAM, CTX:CTX, IMP:IMP, SIZ:SIZ},
            success: (response) => {
                console.log(response);
                $("#ID_FILE_UPLOADER").val("");
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    });

    const GET_FILE_EXE = (NAM, CTX, aes) => {
        NAM = aes.decrypt(NAM, true);
        CTX = aes.decrypt(CTX, true);
        const BLOB_OBJ = FILE_URL.B64_2_BLOB(CTX);
        const BLOB_URL = FILE_URL.GET_BLOB_URL(BLOB_OBJ);
        return [NAM, BLOB_URL, BLOB_OBJ]
    }
    
    const createVisualObj = async (blob_url, filename) => {
        document.getElementById("C_FILES").innerHTML += '<br><br><a href='+blob_url+' download='+filename+'>'+filename+'</a><br><br>';
    }

    $('#OTP_YN').on('change', () => {
        const otp = $('#OTP_YN').prop('checked')? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "../../back-end/class/client_resource_handler.php",
            data: {OTP:otp},
            success: (response) => {
                console.log(response);
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    })

    const sync_2FA_state = () => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {OTPSTATE:1},
            success: (response) => {
                const val = response["2FA"];
                if (val === 1) $('#OTP_YN').prop('checked', true)     
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    }

</script>

<style>

    .FILE_CARDS {

        border: 2px solid white;
        border-radius: 25px;
        width: 80%;
        color: white;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }

    a, h1, h3 {

        color: white;
        font-size: 1.5rem;
    }

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

</style>
