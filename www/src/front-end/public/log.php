<?php   

    require_once '../../back-end/class/system.php';
    require_once '../../back-end/class/sqlc.php';
    require_once '../../back-end/class/response.php';
    //require_once '../resources/OAuth/google/vendor/autoload.php';

    $error = "";

    if (isset($_SERVER['REQUEST_METHOD'])){

        switch ($_SERVER['REQUEST_METHOD']){

            case 'POST': {

                if (isset($_REQUEST['EMAIL']) && isset($_REQUEST['PASS'])){

                    if (filter_var($_REQUEST['EMAIL'], FILTER_VALIDATE_EMAIL)){

                        sqlc::connect();
                        if (sqlc::login($_REQUEST['EMAIL'], $_REQUEST['PASS'])){
                            
                            $id_user = sqlc::get_id_user($_REQUEST['EMAIL']);
                            
                            if (isset($_REQUEST['REM_ME']) && $_REQUEST['REM_ME']){
                                system::remember($id_user); 
                                unset($_REQUEST['REM_ME']);
                            }

                            unset($_REQUEST['EMAIL']);
                            unset($_REQUEST['PASS']);

                            system::redirect_priv_area($id_user);
                            exit;

                        }else { 
                            response::ssend(400, $error, "Incorrect credentials"); 
                        }

                    }else{ 
                        response::ssend(400, $error, "Incorrect email"); 
                    }

                }else { 
                    response::client_error(404); 
                }
            }

            case 'GET': {

                if (isset($_COOKIE['PHPSESSID'])){
                    session_start();
                    if (isset($_SESSION['ID_USER'])){
                        system::redirect_priv_area($_SESSION['ID_USER']);
                    }
                }

                if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
                    if ($_COOKIE['logged']){
                        $tkn = $_COOKIE['rm_tkn'];
                        $htkn = hash("sha256", $tkn);
                        sqlc::connect();
                        $data = sqlc::rem_sel($htkn);
                        if ($data) system::redirect_priv_area($data['id_user']);
                    }
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
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
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
                        <a class="nav-link active" aria-current="page" href="#">Crittografia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="log.php">Accedi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reg.php">Registrati</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Login</div>
                        <div class="card-body">
                            <form action="" method="">
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="EMAIL" type="text" id="email_address" class="form-control" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-6">
                                        <input name="PASS" type="password" id="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="checkbox">
                                            <label>
                                                <input name="REM_ME" type="checkbox" checked="true" id="REM_ME"> <label for="REM_ME">Remember me</label>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>
                                    <a href="#" class="btn btn-link">
                                        Forgot Your Password?
                                    </a>
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
    <script src="../js/login.js"></script>
    <script src="../js/shared.js"></script>
</body>
</html>
