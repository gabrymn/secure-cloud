<?php

    require_once 'script.php';
    main();
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
            <a class="navbar-brand" href="../">HOME</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" style="color:white" href="../signin">Sign in</a>
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

                    <div id="input_error" style="display:none" class="alert alert-danger" onclick="this.style.display='none'" role="alert"></div>
                    
                    <div class="card">
                        <div class="card-header">Sign up</div>
                        <div class="card-body">
                            <form id="signup_form">
                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                    <div class="col-md-6">
                                        <input name="name" type="text" id="id_name" class="form-control" minlength="2" maxlength="30" placeholder="John" oninput="capitalizeFirstLetter('id_name')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="surname" class="col-md-4 col-form-label text-md-right">Surname</label>
                                    <div class="col-md-6">
                                        <input name="surname" type="text" id="id_surname" class="form-control" minlength="2" maxlength="30" placeholder="Smith" oninput="capitalizeFirstLetter('id_surname')" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input name="email" type="email" id="id_email" class="form-control" placeholder="user@example.com" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">New password</label>
                                    <div class="col-md-6">
                                        <input name="pwd" type="password" id="pwd" class="form-control"  placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Confirm password</label>
                                    <div class="col-md-6">
                                        <input name="pwd_confirm" type="password" id="pwd_confirm" class="form-control"  placeholder="••••••" required>
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
    <script src="../../js/url.js"></script>
    <script src="script.js"></script>

</body>
</html>

<script>
    const DOMAIN = '<?php echo $_ENV['DOMAIN']; ?>';
</script>