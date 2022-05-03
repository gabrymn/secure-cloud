<?php

    require_once 'backend-dir.php';

    require_once __BACKEND__ . 'class/system.php';
    require_once __BACKEND__ . 'class/sqlc.php';
    require_once __BACKEND__ . 'class/response.php';
    
    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {
            
                if (isset($_POST['EMAIL']) && isset($_POST['PASS1']) && isset($_POST['PASS2'])){
                    
                    $email = $_POST['EMAIL'];

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                        
                        unset($_POST['EMAIL']);
                        unset($email);
                        response::print(400, $error, "Illegal email format.");

                    }else{                    

                        if ($_POST['PASS1'] !== $_POST['PASS2']){
                            response::print(400, $error, "Passwords doesn't match.");
                        }else{
                            $pass = $_POST['PASS1'];
                            sqlc::connect();

                            if (sqlc::get_id_user($email) > 0){
                                response::print(400, $error, "Email already taken.");
                            }else{
                                sqlc::insert_cred($email, password_hash($pass, PASSWORD_BCRYPT));
                                if (!system::mk_dir($email, __BACKEND__)){
                                    sqlc::del_user_with_email($email);
                                    response::print(500, $error, "Internal server error, try again.");
                                }
                                unset($_POST['EMAIL']);
                                unset($_POST['PASS1']);
                                unset($_POST['PASS2']);
                                header("Location: log.php");
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
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ END BOOTSTRAP FORM ---------->

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/shared.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">end-to-end</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log.php">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reg.php">Sign up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <?php
                        if (isset($error) && $error != "")
                            echo '<div class="alert alert-danger" onclick="this.remove()" role="alert">'.$error.'</div>';
                        unset($error);    
                    ?>
                    <div class="card">
                        <div class="card-header">Sign up</div>
                        <div class="card-body">
                            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="EMAIL" type="email" id="email_address" class="form-control" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-6">
                                        <input name="PASS1" type="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Confirm</label>
                                    <div class="col-md-6">
                                        <input name="PASS2" type="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="../js/reg.js"></script>
    <script src="../js/shared.js"></script>
</body>
</html>

