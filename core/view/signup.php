<?php

    include_once '../model/http_response.php';
    include_once '../model/file_system_handler.php';
    include_once '../model/email.php';
    include_once '../model/mypdo.php';

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'POST': {
            
                if (isset($_POST['email']) && isset($_POST['pwd']) && isset($_POST['name']) && isset($_POST['surname']))
                {
                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                    {
                        unset($_POST['email']);
                        unset($email);
                        http_response_code(400);
                        $error = "Invalid email format";
                    }
                    else
                    {        
                        $email = htmlspecialchars($_POST['email']);            
                        $pass_hash = htmlspecialchars($_POST['pwd_hash']);

                        if (sqlc::connect("USER_TYPE_SELECT") === false)
                            http_response::server_error(500, "Internal server error");

                        if (sqlc::get_id_user($email) > 0)
                        {
                            http_response_code(400);
                            $error =  "Email already taken";
                            sqlc::close();
                            
                        }
                        else
                        {
                            $state = email::is_real($email);

                            if (!$state)
                            {
                                http_response_code(400);
                                $error =  "Email does not exists";
                            }
                            else 
                            {
                                $name = htmlspecialchars($_POST['name']);
                                $surname = htmlspecialchars($_POST['surname']);

                                sqlc::connect("USER_STD_INS");
                                sqlc::insert_cred($email, password_hash($pass, PASSWORD_BCRYPT), $name, $surname);
                                sqlc::close();
                                
                                $creation_user_folder_status = file_system_handler::mk_dir($email, '../model/users_files/');

                                if ($creation_user_folder_status === false)
                                {
                                    sqlc::connect("USER_STD_DEL");
                                    sqlc::del_user_with_email($email);
                                    sqlc::close();

                                    http_response::server_error(500, "Internal server error, try again");
                                }
                                else
                                {
                                    session_start();
                                    $_SESSION['VERIFING_EMAIL'] = 1;
    
                                    $token = new token(50, array("a-z", "A-Z", "0-9"));
                
                                    sqlc::connect("USER_STD_SEL");
                                    $id_user = sqlc::get_id_user($email);
                                    sqlc::close();
                                    sqlc::connect("USER_STD_INS");
                                    sqlc::ins_tkn_verify(intval($id_user), $token->hashed());
                                    sqlc::close();
                        
                                    $sub = "Secure-cloud: verify your email";

                                    $link = "[DOMAIN]/signin.php?";
                                    $link .= "tkn={$token->get()}";
                                    
                                    $msg = "Click this link: $link";
                        
                                    email::send($email, $sub, $msg, $red);
                                }

                                exit;
                            }
                        }
                    }   
                }
                else 
                { 
                    http_response::client_error(404); 
                }
                break;
            }

            case 'GET': {
                break;
            }

            default: {
                http_response::client_error(405);
            }
        }
    }
    else
    {
        http_response::server_error(500);
    }
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
                                        <input name="NAME" type="text" id="id_name" class="form-control" maxlength="30" placeholder="John" oninput="capitalizeFirstLetter('id_name')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">Surname</label>
                                    <div class="col-md-6">
                                        <input name="SURNAME" type="text" id="id_surname" class="form-control" maxlength="30" placeholder="Smith" oninput="capitalizeFirstLetter('id_surname')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="EMAIL" type="email" id="id_email" class="form-control" placeholder="user@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New password</label>
                                    <div class="col-md-6">
                                        <input name="PASS" type="password" id="pwd1" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Confirm password</label>
                                    <div class="col-md-6">
                                        <input name="PASS2" type="password" id="pwd2" class="form-control"  placeholder="••••••" required>
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
</body>
</html>


<script>

    function capitalizeFirstLetter(id) {
        let inputElement = document.getElementById(id);
        let inputValue = inputElement.value;

        let formattedValue = inputValue.replace(/\b\w/g, function (match) {
            return match.toUpperCase();
        });

        inputElement.value = formattedValue;
    }

</script>