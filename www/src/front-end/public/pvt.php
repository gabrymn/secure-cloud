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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h3 class="modal-title" id="ID_MODAL_TITLE">Filename</h3>
            <button id="ID_MODAL_CLOSE" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body" id="ID_MODAL_BODY">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="ID_MODAL_DOWNLOAD">Download</button>
            <button type="button" class="btn btn-danger">Delete</button>
            </div>
            </div>
            </div>
        </div>

        <br><br><br>

        <div id="C_LOADING" style="display:none;margin-left:auto;margin-right:auto" class="lds-dual-ring"></div>

        <h1 id="ID_NFS" class="nfs" style="display:none">Nessun file trovato<h1>

        <div id="CONT_FILES" class="container" style="display:none">
            <div id="C_FILES" class="row">
            </div>
        </div>

        <input type="file" id="ID_FILE_UPLOADER" style="display:none" multiple>

        <br><br><br><br><br>

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
    import getIcon from '../class/icon.js'
    
    const AES = cryptolib['AES']
    const k = "ciao123"
    var ids = [];
    var ids_nms = [];
    var ids_data = [];
    var n_uploads = 0;

    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

    $('document').ready(async () => {
        setLoading("block");
        checkKey();
        sync2FAstate();
        syncData();
    })

    const rd = (min, max) => Math.random() * (max - min) + min

    const showFiles = (ms) => {

        sleep(ms).then(() => {
            $('#ID_NFS').css("display","none")
            for (var key in ids_data)
            {
                $("#id_file_"+key).on('click', (e) => {
                    const id = e.target.id.replaceAll("id_file_", "")
                    var data = JSON.parse(ids_data[id])
                    $('#ID_MODAL_TITLE').html(data.name)
                    $('#ID_MODAL_BODY').html("<strong>Type</strong>: "+data.mime+"<br><strong>Size</strong>: "+data.size+ " byte<br><strong>Upload date</strong>: "+data.upl)
                    $('#ID_MODAL_DOWNLOAD').prop("onclick",null).off("click");
                    $('#ID_MODAL_DOWNLOAD').on('click', () => getCTX(id))
                })
            }

            setLoading("none");
            $("#CONT_FILES").css("display", "block")
        })
    }

    const syncData = () => {
        return $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {REFS:true},
            success: (response) => {
                if (response.file_ids.length > 0) 
                {
                    var aes = new AES(k);
                    response.file_ids.forEach((id) => {
                        getFilePreview(id, aes)
                    });
                    
                    var ms = response.file_ids.length < 10 ? 200 : 900;
                    return new Promise(resolve => showFiles(ms));
                }
                else 
                {
                    sleep(200).then(() => {
                        setLoading("none");
                        $('#ID_NFS').css("display","block")
                    })
                }
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

    const setLoading = state => $("#C_LOADING").css("display", state)        

    const visualF = (fname, x, ttype, filedata = false) => {
        var aline = ""
        if (ttype === 'id')
            aline = `<a id=${x} class='btn btn-info' role='button' data-toggle="modal" data-target="#exampleModal">&#9679;&#9679;&#9679;</a>`;
        else if (ttype === 'href')
            aline = `<a href='${x}' class='btn btn-info' role='button' download='${fname}' style='color:white' data-toggle="modal" data-target="#exampleModal">&#9679;&#9679;&#9679;</a>`

        const arrayName = fname.split(".")
        var ext = arrayName[arrayName.length-1]

        if (fname.length > 17)
        {    
            var t = "";
            if (ext.length > 10) ext = "" 
            for (let i=0; i<10 - ext.length; i++)
                t += fname[i]
            t += " [...]"
            t += "."+ext
            fname = t
        }
        
        const iconFile = getIcon(ext)

        return (`
            <div class="col-6 cardmy">
                <div class="card bg-light">
                    <div class="card-body">
                        <br><center><h1 class="${iconFile}" style="font-size:48px;"></h1></center><br>
                        <center><h5 class="card-title" style="font-size:1rem">${fname}</h5></center><br>
                        <center>${aline}</center>
                    </div>
                </div>
            </div>
        `)
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
                var [url, blob] = GET_FILE_EXE(response.ctx, aes);
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
                var name = response.name.replaceAll("_", "/")
                var mime = aes.decrypt(response.mme, true)
                name = aes.decrypt(name, true)

                var filedata = {
                    name: name,
                    mime: mime,
                    size: response.size,
                    upl: response.upldate
                }

                ids_data[id] = JSON.stringify(filedata)

                var a = visualF(name, "id_file_"+id, "id", filedata)
                ids.push(id)

                ids_nms[id] = JSON.stringify({name:name});
                document.getElementById("C_FILES").innerHTML += a
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

    $("#ID_FILE_UPLOADER").on('change', (e) => {
        checkKey(); 
        var files = Object.values(e.target.files);
        files.forEach((file) => {
            CLIENT_FILE.TO_BASE64(file)
                .then((ctx) => {
                    const fobj = new CLIENT_FILE(file,ctx)
                    var aes = new AES(k);
                    const hash = cryptolib['HASH'].SHA256;
                    const [NAM, CTX, IMP, SIZ, MME] = fobj.ENCRYPT(aes, hash);
                    $.ajax({
                        type: 'POST',
                        url: "../../back-end/class/client_resource_handler.php",
                        data: {NAM: NAM, CTX: CTX, IMP: IMP, SIZ: SIZ, MME: MME},
                        success: (response) => {
                            console.log(response)
                        },
                        error: (xhr) => {
                            console.log(xhr);
                        }
                    });
                })
        })

        $("#ID_NFS").css('display','none')
        $("#CONT_FILES").css('display','none')
        setLoading("block");
        $("#ID_FILE_UPLOADER").val("");

        sleep(300).then(() => {
            document.getElementById("C_FILES").innerHTML = ""
            syncData();
        })

    });

    const GET_FILE_EXE = (CTX, aes) => {
        CTX = aes.decrypt(CTX, true);
        var BLOB = FILE_URL.B64_2_BLOB(CTX);
        var BLOB_URL =  FILE_URL.GET_BLOB_URL(BLOB);
        return [BLOB_URL, BLOB]
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

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

    #CONT_FILES {
        width: 90%;
        margin-left: auto;
        margin-right: auto;
        border-radius: 25px;
        padding-top: 50px;
        padding-bottom: 50px;
        background-color: rgb(90,90,90);
    }

    .cardmy {
        margin: 20px;
        width: 200px;
    }

    .nfs {

        color: white;
        font-size: 4rem;
        font-weight: 100;
        text-align: center;
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