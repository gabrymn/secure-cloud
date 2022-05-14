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

                        // START SESSION
                        {
                            sqlc::connect();

                            // SESSION IS ACTIVE, UPDATE LAST ACTIVITY
                            if (isset($_SESSION['SESSION_STATUS_ACTIVE']))
                            {
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                sqlc::upd_session($session_sc_id);
                            }
                            else
                            // remember me token setted
                            if (isset($_COOKIE['rm_tkn']))
                            {
                                $session = sqlc::sel_session("HTKN", $_COOKIE['rm_tkn']);
                                if ($session)
                                {
                                    // RESUME
                                    $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
                                    $_SESSION['SESSION_SC_ID'] = $session['id'];
                                    $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                    sqlc::upd_session($session_sc_id);
                                }
                                else
                                {
                                    // CREATE
                                    create_session();
                                }
                            }
                            // CREATE NEW SESSION
                            else 
                            {
                                create_session();
                            }

                            $email = sqlc::get_email($_SESSION['ID_USER']);
                        }

                    }
                    else header("Location: log.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
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

    function create_session()
    {
        $session_id = new token(16, "", "", array("0-9", "a-z"));
        $session_id = $session_id->val();
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $htkn = isset($_COOKIE['rm_tkn']) ? $_COOKIE['rm_tkn'] : null;
        sqlc::add_session($session_id, $http_user_agent, $ip, $_SESSION['ID_USER'], $htkn); 
        $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
        $_SESSION['SESSION_SC_ID'] = $session_id;
    }

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
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Cloud Drive</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../back-end/class/out.php">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#" id="ID_UPLOAD">Upload file</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="session_history.php" id="ID_UPLOADX">Cronologia sessioni</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="transfers.php" id="ID_UPLOADY">Transfers</a>
                        </li>
                        <li class="nav-item">
                            <input id="OTP_YN" type="checkbox">
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <br><br>
        <div id="C_LOADING" style="display:none;margin-left:auto;margin-right:auto" class="lds-dual-ring"></div>

        <div class="container">
            <div class="row">
                <br><br><div id="C_FILES" class="FILE_CARDS" style="display:none"></div>
            </div>
        </div>
        <br>

        <input type="file" id="ID_FILE_UPLOADER" style="display:none" multiple>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">
    
    import CLIENT_FILE from '../class/clientfile.js'
    import cryptolib from '../class/cryptolib.js'
    import FILE_URL from '../class/blob.js'

    const AES = cryptolib['AES']
    const k = "ciao123"
    var ids = [];
    var ids_nms = [];
    var n_uploads = 0;

    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    $('document').ready(() => {
        setLoading("block");
        checkKey();
        sync2FAstate();
        syncData();
        showFiles();
        getTranserTables();
    })

    const rd = (min, max) => Math.random() * (max - min) + min

    const showFiles = () => {
        sleep(rd(200,700)).then(() => {
            ids.forEach((id) => {
                $('#'+id).on('click', () => getCTX(id.replace("id_file_", "")))  
            })
            setLoading("none");
            $("#C_FILES").css("display", "block")
        })
    }

    const syncData = () => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {REFS:true},
            success: (response) => {
                if (response.file_ids.length === 0) return;
                const ids = response.file_ids;
                var aes = new AES(k);
                response.file_ids.forEach((id) => {
                    getFilePreview(id, aes)
                });
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

    const getTranserTables = () => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {TRANSFERS:true},
            success: response => {
                console.log(response)
            },
            error: xhr => {
                console.log(xhr)
            }
        })
    }

    const setLoading = state => $("#C_LOADING").css("display", state)        

    const visualF = (fname, x, ttype) => {
        var aline = ""
        if (ttype === 'id')
            aline = `<a id=${x} class='btn btn-info' role='button'>Download</a>`;
        else if (ttype === 'href')
            aline = `<a href='${x}' class='btn btn-info' role='button' download='${fname}' style='color:white'>Download</a>`

        return ( `
            <div class="col-md-9 animated fadeInRight">
                <div class="row">
                    <div class="file-box">
                        <a href="#">
                            <div class="file">
                                <span class="corner"></span>
                                <div class="icon">
                                    <i class="fa fa-bar-chart-o"></i>
                                </div>
                                <div class="file-name">
                                ${fname}
                                <br>
                                ${aline}
                            </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>`
        );
    }

    const getCTX = id => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {ACTION:"CONTENT", ID:id},
            success: response => {
                var a = document.createElement("a");
                document.body.appendChild(a);
                a.style = "display:none";
                var aes = new AES(k);
                var [fn, url, blob] = GET_FILE_EXE(response.name, response.ctx, aes);
                a.href = url;
                a.download = JSON.parse(ids_nms[id]).name;
                a.click();
                document.body.removeChild(a);
            },
            error: xhr => {
                console.log(xhr)
            }
        })
    }
    
    const getFilePreview = (id, aes) => {

        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {ACTION:'PREVIEW',ID:id},
            success: (response) => {
                name = response.name.replaceAll("_", "/")
                name = aes.decrypt(name, true)
                var a = visualF(name, "id_file_"+id, "id")
                ids.push("id_file_"+id)
                ids_nms[id] = JSON.stringify({name:name});
                document.getElementById("C_FILES").innerHTML += '<br><br>'+a+'<br><br>'
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

    const checkKey = () => {
        if (k === null){
            alert("Chiave mancante, e' necessario riaccedere");
            window.location.href = "../../back-end/class/out.php";
            return;
        };
    }

    $("#ID_FILE_UPLOADER").on('change', async (e) => {
        
        checkKey(); 
        var files = Object.values(e.target.files);
        files.forEach((file) => {
            CLIENT_FILE.TO_BASE64(file)
                .then((ctx) => {
                    const fobj = new CLIENT_FILE(file,ctx)
                    var aes = new AES(k);
                    const hash = cryptolib['HASH'].SHA256;
                    const [NAM, CTX, IMP, SIZ] = fobj.ENCRYPT(aes, hash);
                    var [fn, url, blob] = GET_FILE_EXE(NAM, CTX, aes);
                    var a = visualF(file.name, url, "href")
                    document.getElementById("C_FILES").innerHTML += '<br><br>'+a+'<br><br>'
                    $.ajax({
                        type: 'POST',
                        url: "../../back-end/class/client_resource_handler.php",
                        data: {NAM:NAM, CTX:CTX, IMP:IMP, SIZ:SIZ},
                        success: (response) => {
                            console.log(response);
                            n_uploads++;
                            console.log(n_uploads)
                            sleep(100).then(() => {
                                if (n_uploads === 10) 
                                    location.reload();
                            })
                            
                            $("#ID_FILE_UPLOADER").val("");
                        },
                        error: (xhr) => {
                            console.log(xhr);
                        }
                    });
                })
                .catch((error) => {
                    alert("Errore, file troppo grande");
                })
        })
    });

    const GET_FILE_EXE = (NAM, CTX, aes) => {
        NAM = aes.decrypt(NAM, true);
        CTX = aes.decrypt(CTX, true);
        var BLOB = FILE_URL.B64_2_BLOB(CTX);
        var BLOB_URL =  FILE_URL.GET_BLOB_URL(BLOB);
        return [NAM, BLOB_URL, BLOB]
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

    const sync2FAstate = () => {
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

    $("#ID_UPLOAD").on('click', () => {
        $('#ID_FILE_UPLOADER').trigger('click');
    });

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
 

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }


    .file-box {
        float: left;
        width: 220px;
    }
    .file-manager h5 {
    text-transform: uppercase;
    }
    .file-manager {
    list-style: none outside none;
    margin: 0;
    padding: 0;
    }
    .folder-list li a {
    color: #666666;
    display: block;
    padding: 5px 0;
    }
    .folder-list li {
    border-bottom: 1px solid #e7eaec;
    display: block;
    }
    .folder-list li i {
    margin-right: 8px;
    color: #3d4d5d;
    }
    .category-list li a {
    color: #666666;
    display: block;
    padding: 5px 0;
    }
    .category-list li {
    display: block;
    }
    .category-list li i {
    margin-right: 8px;
    color: #3d4d5d;
    }
    .category-list li a .text-navy {
    color: #1ab394;
    }
    .category-list li a .text-primary {
    color: #1c84c6;
    }
    .category-list li a .text-info {
    color: #23c6c8;
    }
    .category-list li a .text-danger {
    color: #EF5352;
    }
    .category-list li a .text-warning {
    color: #F8AC59;
    }
    .file-manager h5.tag-title {
    margin-top: 20px;
    }
    .tag-list li {
    float: left;
    }
    .tag-list li a {
    font-size: 10px;
    background-color: #f3f3f4;
    padding: 5px 12px;
    color: inherit;
    border-radius: 2px;
    border: 1px solid #e7eaec;
    margin-right: 5px;
    margin-top: 5px;
    display: block;
    }
    .file {
    border: 1px solid #e7eaec;
    padding: 0;
    background-color: #ffffff;
    position: relative;
    margin-bottom: 20px;
    margin-right: 20px;
    }
    .file-manager .hr-line-dashed {
    margin: 15px 0;
    }
    .file .icon,
    .file .image {
    height: 100px;
    overflow: hidden;
    }
    .file .icon {
    padding: 15px 10px;
    text-align: center;
    }
    .file-control {
    color: inherit;
    font-size: 11px;
    margin-right: 10px;
    }
    .file-control.active {
    text-decoration: underline;
    }
    .file .icon i {
    font-size: 70px;
    color: #dadada;
    }
    .file .file-name {
    padding: 10px;
    background-color: #f8f8f8;
    border-top: 1px solid #e7eaec;
    }
    .file-name small {
    color: #676a6c;
    }
    ul.tag-list li {
    list-style: none;
    }
    .corner {
    position: absolute;
    display: inline-block;
    width: 0;
    height: 0;
    line-height: 0;
    border: 0.6em solid transparent;
    border-right: 0.6em solid #f1f1f1;
    border-bottom: 0.6em solid #f1f1f1;
    right: 0em;
    bottom: 0em;
    }
    a.compose-mail {
    padding: 8px 10px;
    }
    .mail-search {
    max-width: 300px;
    }
    .ibox {
    clear: both;
    margin-bottom: 25px;
    margin-top: 0;
    padding: 0;
    }
    .ibox.collapsed .ibox-content {
    display: none;
    }
    .ibox.collapsed .fa.fa-chevron-up:before {
    content: "\f078";
    }
    .ibox.collapsed .fa.fa-chevron-down:before {
    content: "\f077";
    }
    .ibox:after,
    .ibox:before {
    display: table;
    }
    .ibox-title {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: #ffffff;
    border-color: #e7eaec;
    border-image: none;
    border-style: solid solid none;
    border-width: 3px 0 0;
    color: inherit;
    margin-bottom: 0;
    padding: 14px 15px 7px;
    min-height: 48px;
    }
    .ibox-content {
    background-color: #ffffff;
    color: inherit;
    padding: 15px 20px 20px 20px;
    border-color: #e7eaec;
    border-image: none;
    border-style: solid solid none;
    border-width: 1px 0;
    }
    .ibox-footer {
    color: inherit;
    border-top: 1px solid #e7eaec;
    font-size: 90%;
    background: #ffffff;
    padding: 10px 15px;
    }

    .lds-dual-ring {
    display: inline-block;
    width: 80px;
    height: 80px;
    }
    .lds-dual-ring:after {
    content: " ";
    display: block;
    width: 64px;
    height: 64px;
    margin: 8px;
    border-radius: 50%;
    border: 6px solid #fff;
    border-color: #fff transparent #fff transparent;
    animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
    0% {
    transform: rotate(0deg);
    }
    100% {
    transform: rotate(360deg);
    }
    }






</style>