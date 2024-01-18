<?php   
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
        <link rel="stylesheet" href="../css/cookie.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">HOME</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" style="font-weight:900;color:white" href="signin.php">Sign in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" style="color:white" href="signup.php">Sign up</a>
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
                        <div class="card">
                            <div class="card-header">Sign in</div>
                            <div class="card-body">
                                <form id="FRM_LGN" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <div class="form-group row">
                                        <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input name="EMAIL" type="email" id="EML" class="form-control" placeholder="user@example.com" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                        <div class="col-md-6">
                                            <input name="PASS" type="password" id="PSW" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6 offset-md-4">
                                            <!--<div class="checkbox">
                                                <label>
                                                    <input name="REM_ME" type="checkbox" id="REM_ME"> <label for="REM_ME">Remember me</label>
                                                </label>
                                            </div>
                                        </div>-->
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                        <!--<a href="password-reset/" class="btn btn-link">
                                            Forgot Your Password?
                                        </a>-->
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </main>
                                

        <div id="ID_COOKIE_BOX" class="row" style="display:none;">
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    </body>

    <script type="module">
    </script>

</html>