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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="cloud.php">CLOUD</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="storage.php">Storage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page" href="transfers.php">Transfers</a>
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
                    </ul>
                </div>
            </div>
        </nav>

        <br><br>
        <h1 id="ID_NFS" class="nfs" style="display:none">No transfers<h1>

        <table style="visibility: hidden;" class="table table-dark tbls" id="ID_TSF_HEAD">
            <thead>
                <tr class="chd">
                    <th scope="col">Name</th>
                    <th scope="col">Transfer date</th>
                    <th scope="col">IP address</th>
                    <th scope="col">Type</th>
                </tr>
            </thead>
            <tbody id="ID_TSF_BODY">
            </tbody>
        </table>

        <br>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">

    import cryptolib from '../class/cryptolib.js'

    const AES = cryptolib['AES']
    var aes = new AES("ciao123")

    $('document').ready(() => {
        getTSFT();
    })

    const getTSFT = () => {
        $.ajax({
            type: 'GET',
            url: "../../back-end/class/client_resource_handler.php",
            data: {TRANSFERS:true},
            success: response => {
                console.log(response)
                var tsf = response.TSF
                if (tsf === 0)
                    $('#ID_NFS').css("display", "block");
                else
                    $('#ID_TSF_HEAD').css("visibility", "visible");

                for (let i = 0; i < tsf.length; i++)
                {
                    tsf[i].filename = tsf[i].filename.replaceAll("_", "/")
                    tsf[i].filename = 
                        aes.decrypt(tsf[i].filename, true)

                    addTsf(tsf[i])
                }
            },
            error: xhr => {
                console.log(xhr)
            }
        })
    }

    const addTsf = (td) => {
        var rowHTML = "";
        td.type = td.type === "u" ? "upload" : "download"
        rowHTML += "<tr>";
            rowHTML += "<td>"+td.filename+"</td>";
            rowHTML += "<td>"+td.transfer_date+"</td>";
            rowHTML += "<td>"+td.ip_address+"</td>";
            rowHTML += "<td>"+td.type+"</td>";
            //rowHTML += "<td>"+td.ip+"</td>";
            //rowHTML += "<td>"+td.client+"</td>";
            //rowHTML += "<td id='ROW_T_"+idRow+"'>"+sd.session_status+"</td>";
        rowHTML += "</tr>";
        document.getElementById("ID_TSF_BODY").innerHTML += rowHTML;
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

    .nfs {
        color: white;
        font-size: 4rem;
        font-weight: 100;
        text-align: center;
    }

    a, h1, h3 {

        color: white;
    }

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

    .tbls {

        margin-left: auto;
        margin-right: auto;
        width: 80%;
    }

    .chd {

        color: #30D2F2;
    }

</style>



