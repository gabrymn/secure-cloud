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
                if (isset($_COOKIE['PHPSESSID']))
                {
                    session_start();
                    if (isset($_SESSION['ID_USER']) && isset($_SESSION['AUTH']))
                    {
                        if (isset($_SESSION['HOTP']))
                        {
                            header("Location: otp.php");
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
                    else header("Location: ../public/signin.php");
                }
                else if (isset($_COOKIE['rm_tkn'])){
                    system::redirect_remember($_COOKIE['rm_tkn']);
                }
                else header("Location: ../public/signin.php");

                break;
            }

            case 'POST': {

                $name = $_POST['NAME'];
                $surname = $_POST['SURNAME'];
                $email = $_POST['EMAIL'];
                $notes = $_POST['NOTES'];
                $old = $_POST['OLD_PSS'];
                $new1 = $_POST['NEW_PSS1'];
                $new2 = $_POST['NEW_PSS2'];

                session_start();
                sqlc::connect();

                if ($old !== "") 
                {
                    if ($new1 !== $new2)
                    {
                        response::client_error(400);
                    }
                    $r = check_pwd(true, $new1);
                    if ($r === 1)
                    {
                        $old_ok = sqlc::pwd_ok($old, $id_user);
                        if ($old_ok)
                        {
                            sqlc::pwd_ch($new1, $_SESSION['ID_USER']);
                        }
                    }
                    else
                    {
                        echo $r; exit;
                    }
                }

                sqlc::upd_user($name, $surname, $email, $notes, $_SESSION['ID_USER']);

                break;  
            }

            default: {

                response::client_error(405);
                break;
            }
        }
    }
    else response::server_error(500);

    sqlc::connect();
    $user = sqlc::sel_user($_SESSION['ID_USER']);
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
    <span class="badge badge-secondary">standard plan</span>
    <div class="text-muted"><small>Joined <?php echo $user['joined'] ?></small></div>
    </div>
    </div>
    </div>
    <ul class="nav nav-tabs">
    <li class="nav-item"><a href="" class="active nav-link">Settings</a></li>
    </ul>
    <div class="tab-content pt-3">
    <div class="tab-pane active">
    <form class="form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="row">
    <div class="col">
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Name</label>
    <input class="form-control" type="text" name="NAME" placeholder="John" value="<?php echo $user['name']; ?>" required>
    </div>
    </div>
    <div class="col">
    <div class="form-group">
    <label>Surname</label>
    <input class="form-control" type="text" name="SURNAME" placeholder="Smith" value="<?php echo $user['surname']; ?>" required>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>E-Mail Address</label>
    <input class="form-control" type="text" name="EMAIL" placeholder="user@example.com" value="<?php echo $user['email']; ?>" required>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col mb-3">
    <div class="form-group">
    <label>Notes</label>
    <textarea name="NOTES" maxlength="65535" class="form-control" rows="5" placeholder="Notes"><?php echo $user['notes']; ?></textarea>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col-12 col-sm-6 mb-3">
    <div class="mb-2"><b>Change Password</b></div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Current Password</label>
    <input name="OLD_PSS" class="form-control" type="password" placeholder="••••••">
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>New Password</label>
    <input name="NEW_PSS1" class="form-control" type="password" placeholder="••••••">
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col">
    <div class="form-group">
    <label>Confirm <span class="d-none d-xl-inline">Password</span></label>
    <input name="NEW_PSS2" class="form-control" type="password" placeholder="••••••"></div>
    </div>
    </div>
    </div>
    </div>
    <div class="row">
    <div class="col d-flex justify-content-end">
    <button class="btn btn-primary" type="submit">Save Changes</button>
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

    "use strict";

    $('document').ready(() => {
    });

</script>

<style>


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