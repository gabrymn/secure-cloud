<?php

    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    require_once '../../back-end/class/system.php';

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
                            header("Location: ../public/otp.php");
                            exit;
                        }

                        // START SESSION
                        {

                            // SESSION IS ACTIVE, UPDATE LAST ACTIVITY
                            if (isset($_SESSION['SESSION_STATUS_ACTIVE']))
                            {
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                sqlc::connect("USER_STD_UPD");
                                sqlc::upd_session($session_sc_id);
                                sqlc::close();
                            }
                            else
                            // remember me token setted
                            if (isset($_COOKIE['rm_tkn']))
                            {
                                sqlc::connect("USER_STD_SEL");
                                $session = sqlc::sel_session("HTKN", $_COOKIE['rm_tkn']);
                                sqlc::close();

                                if ($session)
                                {
                                    // RESUME
                                    $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
                                    $_SESSION['SESSION_SC_ID'] = $session['id'];
                                    $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                    sqlc::connect("USER_STD_UPD");
                                    sqlc::upd_session($session_sc_id);
                                    sqlc::close();
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

                            sqlc::connect("USER_STD_SEL");
                            $email = sqlc::get_email($_SESSION['ID_USER']);
                            sqlc::close();
                        }

                    }
                    else header("Location: ../public/signin.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }
                else header("Location: ../public/signin.php");

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
        sqlc::connect("USER_STD_INS");
        sqlc::add_session($session_id, $http_user_agent, $ip, $_SESSION['ID_USER'], $htkn); 
        sqlc::close();
        $_SESSION['SESSION_STATUS_ACTIVE'] = 1;
        $_SESSION['SESSION_SC_ID'] = $session_id;
    }

    sqlc::connect("USER_STD_SEL");
    $size = sqlc::get_used_space($_SESSION['ID_USER']);
    $tot = sqlc::sel_plan($_SESSION['ID_USER'])['gb'];
    sqlc::close();
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

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid #157EFB">
            <div class="container-fluid">
                <a class="navbar-brand" style="font-weight:900" href="cloud.php">CLOUD</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="storage.php">Storage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="transfers.php">Transfers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="sessions.php">Sessions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="settings.php">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page"></a>
                        </li>
                        <button onclick="window.location.href='../../back-end/class/out.php'" class="btn btn-dark btn-secondary">
                          <i class="fa fa-sign-out"></i>
                          <span>Logout</span>
                        </button>

                        <!--
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
                            <input id="OTP_YN" type="checKBox">
                        </li>-->
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
            <button type="button" class="btn btn-success" id="ID_MODAL_SHOW">SHOW</button>
            <button type="button" class="btn btn-primary" id="ID_MODAL_DOWNLOAD">DOWNLOAD</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="ID_MODAL_DEL">DELETE</button>
            </div>
            </div>
            </div>
        </div>

        <br><br><br>

        <div class="uploader"><button id="ID_UPLOAD" type="button" class="btn btn-primary btn-lg btn-block">Upload File <i class="fa fa-cloud-upload" aria-hidden="true"></i></i></button></div>

        <br><br>
        <h1 id="ID_NFS" class="nfs" style="display:none">...<h1>

        <div id="C_LOADING" style="display:none;margin-left:auto;margin-right:auto" class="lds-dual-ring"></div>

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
    
    "use strict"

    import CLIENT_FILE from '../class/clientfile.js'
    import FILE_URL from '../class/blob.js'
    import cryptolib from '../class/cryptolib.js'
    import FileViewer from '../class/fileViewer.js'
    import getIcon from '../class/icon.js'
    import Cookie from '../class/cookie.js';
    import Polling from "../class/polling.js";
    import {getpk,checkpk} from "../class/pvtk.js"

    var aes = new cryptolib['AES'](getpk())

    var actStg = "<?php echo $size; ?>";
    const totStg = "<?php echo $tot; ?>" * 1000000000;
    // 100000 => 100 KB

    var ids = [];
    var ids_nms = [];
    var ids_data = [];
    var n_uploads = 0;
    var fileNames = [];
    var fid = [];

    var getSessionStatus
    var SESSION_SC_ID;

    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms))
    const setLoading = state => $("#C_LOADING").css("display", state)  
    $("#ID_UPLOAD").on('click', () => {
        $('#ID_FILE_UPLOADER').trigger('click');
    });

    $('document').ready(() => {
        checkpk()
        SESSION_SC_ID = ("<?php echo $_SESSION['SESSION_SC_ID']; ?>");
        setLoading("block");
        sync2FAstate();
        syncData();
        syncSession();
        getSessionStatus = new Polling(sessionStatus, 5000);
        getSessionStatus.Start();
    })

    const sessionStatus = () => {
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSION_ID:SESSION_SC_ID},
            type: "GET",
            success: (response) => {
                //console.info("session status "+response);
                if (response == 0)
                {
                    alert("Sessione terminata, clicca ok per continuare");
                    window.location.href = "../../back-end/class/out.php"
                }
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    }
    const syncSession = () => {
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSIONS_DATA:true},
            type: "GET",
            success: (response) => {
                SESSION_SC_ID = "<?php echo $_SESSION['SESSION_SC_ID']; ?>";
            },
            error: (xhr) => {
                console.log(xhr);
            } 
        });
    }

    const showFiles = (ms) => {

        sleep(ms).then(() => {
            $('#ID_NFS').css("display","none")
            for (var key in ids_data)
            {
                $("#id_file_"+key).on('click', (e) => {
                    const id = e.target.id.replaceAll("id_file_", "")
                    var data = JSON.parse(ids_data[id])
                    $('#ID_MODAL_TITLE').html(data.name)
                    $('#ID_MODAL_BODY').html("<strong>Type</strong>: "+data.mime+"<br><strong>Size: </strong>"+getSizeString(data.size)+"<br><strong>Upload date</strong>: "+data.upl)
                    $('#ID_MODAL_DOWNLOAD').prop("onclick",null).off("click");
                    $('#ID_MODAL_DOWNLOAD').on('click', () => getCTX(id))
                    $('#ID_MODAL_SHOW').prop("onclick",null).off("click");
                    $('#ID_MODAL_SHOW').on('click', () => FileViewer.Show(id, aes, FILE_URL))
                    $('#ID_MODAL_DEL').prop("onclick",null).off("click");
                    $('#ID_MODAL_DEL').on('click', () => fdel(id))
                })
            }

            setLoading("none");
            $("#CONT_FILES").css("display", "block")
        })
    }

    const getSizeString = (bytes) => bytes < 1000 ? Math.round(bytes*100)/100 + " B" : bytes >= 1000 && bytes < 1000000 ? Math.round(bytes/1000*100)/100 + " KB" : bytes >= 1000000  && bytes < 1000000000 ? Math.round(bytes/1000000*100)/100 + " MB" : Math.round(bytes/1000000000*100)/100 + " GB"; 

    const fdel = id => {
        $("#id_view_"+id).remove()

        $.ajax({
            type: 'DELETE',
            url: "../../back-end/class/client_resource_handler.php?id="+id,
            success: response => {
                console.log(response)
            },
            error: xhr => {
                console.log(xhr)
            }
        })

        const name = fid[id];
        const index = fileNames.indexOf(name);
        fileNames.splice(index, 1);

        if (fileNames.length === 0)
        {
            $("#CONT_FILES").css("display", "none")
            $('#ID_NFS').css("display","block")
        }
    }

    const syncData = () => {
        return $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {REFS:true},
            success: (response) => {
                if (response.file_ids.length > 0) 
                {
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


    const createFilePreview = (fname, x, ttype, filedata = false) => {
        var aline = ""
        aline = `<a id=${x} class='btn btn-info' role='button' data-toggle="modal" data-target="#exampleModal">&#9679;&#9679;&#9679;</a>`;

        var ext = ""

        if (fname.includes('.'))
        {
            const arrayName = fname.split(".")
            var ext = "."+arrayName[arrayName.length-1]
        }

        if (fname.length > 17)
        {    
            var t = "";
            if (ext.length > 10) ext = "" 
            for (let i=0; i<10 - ext.length; i++)
                t += fname[i]
            t += " [...]"
            t += ext
            fname = t
        }
        
        const iconFile = getIcon(ext)
        const viewID = x.replace("id_file_", "")

        return (`
            <div id="id_view_${viewID}" class="col-6 cardmy">
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
            data: {ACTION:"CONTENT", ID:id, DOWNLOAD:true},
            success: response => {
                var a = document.createElement("a");
                document.body.appendChild(a);
                a.style = "display:none";
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

                fileNames.push(name)
                fid[id] = name

                ids_data[id] = JSON.stringify(filedata)

                var fp = createFilePreview(name, "id_file_"+id, "id", filedata)
                ids.push(id)

                ids_nms[id] = JSON.stringify({name:name});
                document.getElementById("C_FILES").innerHTML += fp
            },
            error: (xhr) => {
                console.log(xhr);
            }
        });
    }

    const handleExists = (filename) => {

        if (fileNames.includes(filename))
        {
            const ext = filename.includes('.') ? "."+filename.split('.')[filename.split('.').length-1] : ""
            const name = ext === '' ? filename : filename.replace(ext, '')
            
            var add = 1;
            while (fileNames.includes(filename)) 
            {
                filename = name+"("+add+')'+ext;
                add++;
            }
        }
        return filename
    }

    const checkStorage = (uploadBytes) => {
        
        if ((Number(actStg) + uploadBytes) <= Number(totStg))
        {
            actStg = Number(actStg) + Number(uploadBytes)
            return true;
        }
        return false;
    }

    $("#ID_FILE_UPLOADER").on('change', (e) => {
        var files = Object.values(e.target.files);

        files.forEach((file) => {
            if (!checkStorage(Number(file.size)))
            {
                alert("Spazio esaurito, elimina dei contenuti e riprova")
                return
            }
            CLIENT_FILE.TO_BASE64(file)
                .then((ctx) => {

                    var name = handleExists(file.name)
                    const fobj = new CLIENT_FILE(file,ctx,name)

                    const hash = cryptolib['HASH'].SHA256;
                    const [NAM, CTX, IMP, SIZ, MME] = fobj.ENCRYPT(aes, hash);
                    $.ajax({
                        type: 'POST',
                        url: "../../back-end/class/client_resource_handler.php",
                        data: {NAM: NAM, CTX: CTX, IMP: IMP, SIZ: SIZ, MME: MME},
                        success: (response) => {
                            console.log(response)
                            location.reload()
                        },
                        error: (xhr) => {
                            console.log(xhr);
                        }
                    });
                })
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

</script>

<style>

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

    .uploader {

        width: 70%;
        margin-left: auto;
        margin-right: auto;
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