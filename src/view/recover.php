<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Secure Cloud</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/shared.css">
        <link rel="stylesheet" href="css/pages/home.css">
        <link href="img/favicon.svg" rel="icon" type="image/x-icon" >
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid #157EFB">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">Home</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" style="color:white" href="/signin">Sign in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" style="color:white" href="/signup">Sign up</a>
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
                    
                    <div id="error_div" class="alert alert-danger" style="display:none" onclick="this.style.display='none'" role="alert"></div>

                        <div class="card">
                            
                            <div class="card-header">Account Recovery</div>

                            <div id="email_box" class="card-body">
                                <form id="email_form">
                                    <div class="form-group row">
                                        <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input name="email" type="email" id="email" class="form-control" placeholder="user@example.com" required autofocus>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div id="recoverykey_box" class="card-body" style="display:none">
                                <form id="recoverykey_form">

                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Recovery Key</label>
                                        <div class="col-md-6">
                                            <input name="recoverykey" type="password" id="recoverykey" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            </div>




                            <div id="password_reset_box" class="card-body" style="display:none">
                                <form id="password_reset_form">
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">New Passwor</label>
                                        <div class="col-md-6">
                                            <input name="pwd" type="password" id="pwd" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Confirm Passwor</label>
                                        <div class="col-md-6">
                                            <input name="pwd_confirm" type="password" id="pwd_confirm" class="form-control" placeholder="••••••" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            </div>


                            <div id="success_box" class="card-body" style="display:none">
                                <h1 class="card-title text-center mb-4">Password reimposta correttamente</h1>
                                <form>
                                    <div class="text-center">
                                        <button class="btn btn-primary" type="button" onclick="window.location.href='/signin'">Sign in</button>
                                    </div>
                                </form>
                            </div>


                        </div>


                    </div>
                </div>
            </div>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

        <script src="js/pages/recover.js"></script>
        <script src="js/url.js"></script>
    </body>
</html>