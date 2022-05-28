<?php

    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/crypto.php';

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD']) 
        {

            case 'GET': {
                if (isset($_GET['PLANS']) && count($_GET) === 1)
                {
                    sqlc::connect("USER_STD_UPD");
                    $plans = sqlc::sel_plans();
                    session_start();
                    $myplan = sqlc::sel_plan($_SESSION['ID_USER']);
                    if (($key = array_search($myplan, $plans)) !== false) 
                    {
                        unset($plans[$key]);
                    }
                    sqlc::close();
                    response::successful(200, false, array("myplan" => $myplan, "plans" => array_values($plans)));
                    exit;
                }

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
                                sqlc::connect("USER_STD_UPD");
                                $session_sc_id = $_SESSION['SESSION_SC_ID'];
                                sqlc::upd_session($session_sc_id);
                                sqlc::close();
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
                    else header("Location: ../public/signin.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }
                else header("Location: ../public/signin.php");

                break;
            }

            case 'POST': {

                if (isset($_POST['CHANGE_PLAN']))
                {
                    $plan = $_POST['CHANGE_PLAN'];
                    sqlc::connect("USER_STD_SEL");
                    $id_plan = sqlc::sel_id_from_planame($plan);
                    sqlc::close();
                    session_start();
                    $id_user = $_SESSION['ID_USER'];
                    sqlc::connect("USER_STD_UPD");
                    sqlc::upd_plan($id_user, $id_plan);
                    sqlc::close();
                    response::successful(200, "Plan updated: $plan");
                    exit;
                }
                else
                {
                    $name = $_POST['name'];
                    $surname = $_POST['surname'];
                    $notes = $_POST['notes'];

                    session_start();

                    if (isset($_POST['new1']) && isset($_POST['old']))
                    {
                        $psw = $_POST['new1'];
                        $old = $_POST['old'];

                        sqlc::connect("USER_STD_SEL");
                        $old_ok = sqlc::pwd_ok($old, $_SESSION['ID_USER']);
                        sqlc::close();
                        
                        if ($old_ok)
                        {
                            $r = check_pwd(true, $psw);
                            if ($r === 1)
                            {
                                $psw = password_hash($psw, PASSWORD_BCRYPT);
                                sqlc::connect("USER_STD_UPD");
                                sqlc::pwd_ch($psw, $_SESSION['ID_USER']);
                                sqlc::close();
                            }
                            else
                            {
                                http_response_code(400);
                                echo $r;
                                exit;
                            }
                        }
                        else
                        {
                            http_response_code(400);
                            echo "Password non corretta";
                            exit;
                        }
                    }

                    sqlc::connect("USER_STD_UPD");
                    sqlc::upd_user($name, $surname, $notes, $_SESSION['ID_USER']);
                    sqlc::close();
                    http_response_code(200);
                    echo "Changes saved correctly";
                    exit;
                }

                break;  
            }

            default: {

                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);

    sqlc::connect("USER_STD_SEL");
    $user = sqlc::sel_user($_SESSION['ID_USER']);
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
    <a class="nav-link active" aria-current="page" href="transfers.php">Transfers</a>
    </li>
    <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="sessions.php">Sessions</a>
    </li>
    <li class="nav-item">
    <a class="nav-link active" style="font-weight:900" aria-current="page" href="settings.php">Settings</a>
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

    <div class="container">
    <div class="row flex-lg-nowrap">

    <div class="col">
    <div class="row">
    <div class="col mb-3">
    <div class="card">
    <div class="card-body">
    <div class="e-profile">
    <div class="row">
    <div class="col-12 col-sm-auto mb-3">

    </div>
    <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
    <div class="text-center text-sm-left mb-2 mb-sm-0">
    <h2 class="pt-sm-2 pb-1 mb-0 text-nowrap">Edit your profile</h2>
    <br>
    </div>
    <div class="text-center text-sm-right">
    <span id="ID_BOX_PLAN" class="badge badge-secondary"></span>
    <div class="text-muted"><small>Joined <?php echo $user['joined'] ?></small></div>
    </div>
    </div>
    </div>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a href="" class="active nav-link">Settings</a></li>
    </ul>
    <div class="tab-content pt-3">
    <div class="tab-pane active">
    <form id="ID_INF_FORM" class="form" onsubmit="return false">
    <div class="row">
    <div class="col">
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Name</label>
    <input id="ID_NAME" class="form-control" type="text" name="NAME" placeholder="John" value="<?php echo $user['name']; ?>" required>
    </div>
    </div>
    <div class="col">
    <div class="form-group">
    <label>Surname</label>
    <input id="ID_SURNAME" class="form-control" type="text" name="SURNAME" placeholder="Smith" value="<?php echo $user['surname']; ?>" required>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>E-Mail Address</label>
    <input class="form-control" type="text" placeholder="user@example.com" value="<?php echo $user['email']; ?>">
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col mb-3">
    <div class="form-group">
    <label>Notes</label>
    <textarea id="ID_NOTES_" name="NOTES" maxlength="65535" class="form-control" rows="5" placeholder="Notes"></textarea>
    </div>
    </div>
    </div>
    </div>
    </div>
    <br>
    <li class="nav-item">
        <p class="checkboxtext">2-Factor Authentication (2FA) &nbsp;&nbsp;</p> 
        <input id="OTP_YN" type="checKBox">
    </li>
    <br>
    <li class="nav-item">
        <p class="checkboxtext">Your plan: &nbsp;&nbsp;</p>
        <select id="ID_PLANS" class="browser-default custom-select" style="width:40%">
        </select>
    </li>
    <br><br>
    <div class="row" style="float:left">
        <div class="col d-flex justify-content-end">
            <button id="ID_DEL_ACC" class="btn btn-danger" type="submit">Delete your account</button>
        </div>
    </div>
    <br><br>
    <br><br>
    <div class="row">
    <div class="col-12 col-sm-6 mb-3">
    <div class="mb-2"><b>Change Password</b></div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Current Password</label>
    <input id="OLD_PSS" class="form-control" type="password" placeholder="••••••">
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>New Password</label>
    <input id="NEW_PSS1" class="form-control" type="password" placeholder="••••••">
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Confirm <span class="d-none d-xl-inline">Password</span></label>
    <input id="NEW_PSS2" class="form-control" type="password" placeholder="••••••"></div>
    </div>
    </div>
    </div>
    </div>
        <div class="row">
            <div class="col d-flex justify-content-end">
                <button id="ID_SAVE_CH" class="btn btn-primary" type="submit">Save Changes</button>
            </div>
        </div>
    </form>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>

    </div>

    </div>
    </div>
    </div>

    <br><br>

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

    import cryptolib from '../class/cryptolib.js'
    import Polling from '../class/polling.js'
    import {getpk, checkpk} from "../class/pvtk.js"
    
    var aes = new cryptolib['AES'](getpk());
    var SESSION_SC_ID;

    $('document').ready(() => {
        checkpk()
        SESSION_SC_ID = ("<?php echo $_SESSION['SESSION_SC_ID']; ?>");
        sync2FAstate()
        getPlans()
        decryptNotes()
        syncSession();
        var getSessionStatus = new Polling(sessionStatus, 5000);
        getSessionStatus.Start();
    });

    const sessionStatus = () => {
        $.ajax({
            url: "../../back-end/class/sessions_handler.php",
            data: {SESSION_ID:SESSION_SC_ID},
            type: "GET",
            success: (response) => {
                console.info("session status "+response);
                if (response == 0)
                {
                    getSessionStatus.Stop();
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

    const decryptNotes = () => {
        $('#ID_NOTES_').val(aes.decrypt("<?php echo $user['notes']; ?>", true))
    }

    $('#ID_SAVE_CH').on('click', () => {

        const name = $('#ID_NAME').val();
        const surname = $('#ID_SURNAME').val();
        var notes = $('#ID_NOTES_').val();
        const old = $('#OLD_PSS').val();
        const new1 = $('#NEW_PSS1').val();
        const new2 = $('#NEW_PSS2').val();
        var data;

        notes = aes.encrypt(notes, true);

        if (old === "")
            data = {name, surname, notes}
        else
        {
            if (new1 === new2)
                data = {name, surname, notes, new1, old}
            else {
                alert("Password differenti")
                return
            }
        }

        $.ajax({
            type: 'POST',
            url: "<?php echo $_SERVER['PHP_SELF']; ?>",
            data: data,
            success: response => {
                alert(response);
            },
            error: xhr => {
                alert(xhr.responseText);
            }
        })
    })

    $('#ID_DEL_ACC').on('click', () => {
        if (confirm('Stai per cancellare il tuo account, continuare?')) {
            $.ajax({
                type: 'DELETE',
                url: "../../back-end/class/delete_acc.php",
                success: (r) => {
                    window.location.href = "../../back-end/class/out.php"
                    return
                }
            })
        }
    })

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

    const changePlanCSS = (planame) => {

        $('#ID_BOX_PLAN').html(planame + " plan");
        if (planame === "Standard")
            $('#ID_BOX_PLAN').css("background-color", "var(--silver)");
        else
            $('#ID_BOX_PLAN').css("background-color", "var(--gold)");
    }

    $('#ID_PLANS').on('change', () => {
        var val = $('#ID_PLANS').val().split("");
        var planame = "";
        for (let i=0; val[i] !== " "; i++)
            planame += val[i];

        changePlanCSS(planame);

        $.ajax({
            type: 'POST',
            url: "<?php $_SERVER['PHP_SELF']; ?>",
            data: {CHANGE_PLAN:planame},
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

    const getPlans = () => {
        $.ajax({
            type: 'GET',
            url: "<?php $_SERVER['PHP_SELF']; ?>",
            data: {PLANS:true},
            success: (response) => {
                console.log(response)
                document.getElementById('ID_PLANS').innerHTML += "<option selected=''>"+response.myplan.name+ " (" + response.myplan.gb+ " GB)" +"</option>";
                changePlanCSS(response.myplan.name);
                response.plans.forEach((plan) => 
                    document.getElementById('ID_PLANS').innerHTML += "<option>"+plan.name + " (" + plan.gb+ " GB)" +"</option>"
                )
            },
            error: (xhr) => {
                console.log(xhr);
            }
        })
    }

</script>

<style>

    :root {
        --gold: #BBA14F;
        --silver: #6C757D;
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


    input[type=checkbox]
    {
        -ms-transform: scale(2); /* IE */
        -moz-transform: scale(2); /* FF */
        -webkit-transform: scale(2); /* Safari and Chrome */
        -o-transform: scale(2); /* Opera */
        transform: scale(2);
        padding: 10px;
    }

    input[type=checkbox]:hover
    {
        cursor: pointer;
    }

    .checkboxtext
    {
        font-size: 110%;
        display: inline;
    }


</style>