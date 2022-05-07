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
                                    sqlc::upd_session($session_sc_id);
                                }
                                else
                                {
                                    // CREATE NEW SESSION
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

<!------ START BOOTSTRAP FORM ---------->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>secure-cloud</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/shared.css">
        <link rel="stylesheet" href="../css/login.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><?php echo $email; ?></a>
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
                            <a class="nav-link active" aria-current="page" href="session_history.php" id="ID_UPLOAD">Cronologia sessioni</a>
                        </li>
                        <li class="nav-item">
                            <input id="OTP_YN" type="checkbox">
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <br><br>

        <table class="table table-dark" style="width: 80%; margin-left: auto; margin-right: auto;" id="ID_SESSIONS">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">Client</th>
                    <th scope="col">OS</th>
                    <th scope="col">Device</th>
                    <th scope="col">Last activity</th>
                    <th scope="col">Session status</th>
                </tr>
            </thead>
            <tbody id="ID_TBL_BODY">
            </tbody>
        </table>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<script type="module">

    "use strict";
    var SESSION_SC_ID;

    $('document').ready(() => {
        syncSessions();
    });

    const addSession = (sd) => {
        var rowHTML = "";
        sd.session_status = sd.session_status? sd.id === SESSION_SC_ID ? 'Actual' : 'Active': 'Expired';
        rowHTML += "<tr>";
            rowHTML += "<td>"+sd.id+"</td>";
            rowHTML += "<td>"+sd.ip+"</td>";
            rowHTML += "<td>"+sd.client+"</td>";
            rowHTML += "<td>"+sd.os+"</td>";
            rowHTML += "<td>"+sd.device+"</td>";
            rowHTML += "<td>"+sd.last_time+"</td>";
            rowHTML += "<td>"+sd.session_status+"</td>";
        rowHTML += "</tr>";
        document.getElementById("ID_TBL_BODY").innerHTML += rowHTML;
    }

    const syncSessions = () => {

        const id_user = <?php echo $_SESSION['ID_USER']; ?>;
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {ID_USER: id_user},
            type: "GET",
            success: (response) => {
                console.log(response);
                SESSION_SC_ID = "<?php echo $_SESSION['SESSION_SC_ID']; ?>";
                for (let i=0; i<response.sessions.length; i++){
                    addSession(response.sessions[i]);
                }
            },
            error: (xhr) => {
                console.log(xhr);
            } 
        });
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
    }

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

</style>