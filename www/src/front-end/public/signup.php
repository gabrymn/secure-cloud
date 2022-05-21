<?php

    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    require_once '../../back-end/class/email.php';
    
    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {
            
                if (isset($_POST['EMAIL']) && isset($_POST['PASS']) && isset($_POST['NAME']) && isset($_POST['SURNAME'])){
                    
                    $email = $_POST['EMAIL'];

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        unset($_POST['EMAIL']);
                        unset($email);
                        response::print(400, $error, "Illegal email format.");
                    }
                    else
                    {                    
                        $pass = $_POST['PASS'];
                        sqlc::connect();

                        if (sqlc::get_id_user($email) > 0){
                            response::print(400, $error, "Email already taken.");
                        }else{

                            $state = email_is_real($email);

                            if (!$state)
                            {
                                response::print(400, $error, "Email does not exists");
                            }
                            else 
                            {
                                $name = $_POST['NAME'];
                                $surname = $_POST['SURNAME'];
                                sqlc::insert_cred($email, password_hash($pass, PASSWORD_BCRYPT), $name, $surname);
                                if (!system::mk_dir($email, '../../back-end/'))
                                {
                                    sqlc::del_user_with_email($email);
                                    response::print(500, $error, "Internal server error, try again.");
                                }

                                session_start();
                                $_SESSION['VERIFING_EMAIL'] = 1;
                                system::verify($email, 1);
                                exit;
                            }
                        }
                    }
                }
                else { response::client_error(404); }
                break;
            }

            case 'GET': {
                // ok
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
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE CLOUD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/shared.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/cookie.css">
    <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
        <div class="container-fluid">
            <a class="navbar-brand" href="../public/">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" style="color:white" href="signin.php">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" style="font-weight:900;color:white" href="signup.php">Sign up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <?php
                        if (isset($error) && $error != "")
                            echo '<div class="alert alert-danger" onclick="this.remove()" role="alert">'.$error.'</div>';
                        unset($error);    
                    ?>
                    <div id="ERROR_PDM" style="display:none" class="alert alert-danger" onclick="this.style.display='none'" role="alert">Password does not match</div>
                    <div class="card">
                        <div class="card-header">Sign up</div>
                        <div class="card-body">
                            <form id="ID_REG_FORM" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">Name</label>
                                    <div class="col-md-6">
                                        <input name="NAME" type="text" id="NAME_" class="form-control" maxlength="30" placeholder="John" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">Surname</label>
                                    <div class="col-md-6">
                                        <input name="SURNAME" type="text" id="SURNAME_" class="form-control" maxlength="30" placeholder="Smith" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="EMAIL" type="email" id="EMAIL_" class="form-control" placeholder="user@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New password</label>
                                    <div class="col-md-6">
                                        <input name="PASS" type="password" id="PASS_1" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Confirm password</label>
                                    <div class="col-md-6">
                                        <input name="PASS2" type="password" id="PASS_2" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>

    <div id="ID_COOKIE_BOX" class="row" style="display:none">
        <div class="col-md-4 col-sm-12 button-fixed">
        <div class="p-3 pb-4 bg-custom text-white">
        <div class="row">
        <div class="col-10">
        <h1>Allow Cookies</h1>
        </div>
        <div class="col-2 text-center">
        <i class="fas fa-times"></i>
        </div>
        </div>
        <p>Utilizziamo i cookie per migliorare la tua esperienza</p>
        <button id="ID_COOKIE_A" type="button" class="btn btn-light w-100">Accept</button>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    <script src="../js/reg.js"></script>
    <script src="../js/shared.js"></script>
</body>
</html>


<script type="module">

    import Cookie from "../class/cookie.js"

    $('#ID_REG_FORM').on('submit', () => {
        if ($('#PASS_1').val() !== $('#PASS_2').val())
        {
            $('#ERROR_PDM').css("display", "block")
            return false
        }
    })

    $('document').ready(() => {
        if (Cookie.Get("allow__") === null)
            $('#ID_COOKIE_BOX').css("display", "block")
    })
    
    $('#ID_COOKIE_A').on('click', () => {
        Cookie.Set("allow__", "true", 2)
        $('#ID_COOKIE_BOX').css("display", "none")
    })

</script>

