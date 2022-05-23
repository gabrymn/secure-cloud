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
                            sqlc::connect("USER_STD_UPD");

                            // SESSION IS ACTIVE, UPDATE LAST ACTIVITY
                            if (isset($_SESSION['SESSION_STATUS_ACTIVE']))
                            {
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
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

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="cloud.php">CLOUD</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page" href="storage.php">Storage</a>
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
                    </ul>
                </div>
            </div>
        </nav>

        <br><br><br>


        <h5 id="ID_NFS" class="nfs">Spazio occupato<h5>
        <br><br>
        <div class="progress" style="width:80%;margin-left:auto;margin-right:auto;">
            <div id="ID_PB" class="progress-bar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <br><br>
        <p id="ID_FS" style="font-size:1.7rem" class="nfs" >Hai a disposizione ancora </p>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">
    
    import Polling from '../class/polling.js'

    var SESSION_SC_ID;

    $('document').ready(() => {
        SESSION_SC_ID = ("<?php echo $_SESSION['SESSION_SC_ID']; ?>");
        draw();
        syncSession();
        var getSessionStatus = new Polling(sessionStatus, 5000);
        getSessionStatus.Start();
    })

    const sessionStatus = () => {
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSION_ID:SESSION_SC_ID},
            type: "GET",
            success: (response) => {
                console.info("session status "+response);
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

    const draw = () => {
        const s = "<?php echo $size; ?>";
        var t = "<?php echo $tot; ?>";
        const tot = t;
        var u = "";
        var used = "";
        if (s < 1000)
        {
            used += s/100*100 
            u = "B"
        }else if (s >= 1000 && s < 1000000)
        {
            used += s/1000;
            u = "KB";
        }
        else if (s >= 1000000 && s < 1000000)
        {
            used += s/1000000;
            u = "MB";
        }
        else 
        {
            used += s/1000000000;
            u = "GB";
        }

        used = Math.round(used*100)/100;

        t *= 1000000000;
        var perc = Math.floor(s/t*100*100)/100;
        $('#ID_PB').css("width", Math.floor(perc*10000)/10000 + "%")
        if (perc === 0)
            perc = " < 1";
        if (s == 0)
            perc = " 0";
        if (perc >= 70 && perc < 80)
            $('#ID_PB').css("background-color", "yellow")
        else if (perc >= 80 && perc <= 100)
            $('#ID_PB').css("background-color", "#FE130F")
        else 
            $('#ID_PB').css("background-color", "#00FF00")
        $('#ID_NFS').html($('#ID_NFS').html() +": " + perc + "%" + " (" + used + " " + u + ")");
        $('#ID_FS').html($('#ID_FS').html() +" "+Math.floor((parseInt(t)-parseInt(s))/1000000000*100)/100+" GB su "+tot+" GB");
    }   

</script>

<style>

    .nfs {
        color: white;
        font-size: 4rem;
        font-weight: 100;
        text-align: center;
    }

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