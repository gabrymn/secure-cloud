<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Secure Cloud</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="css/shared.css">
        <link href="img/favicon.svg" rel="icon" type="image/x-icon" >
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    </head>
    <body>
        
        <?php echo $navbar; ?>

        <br>
        <main class="signin-form">
            <div class="cotainer">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        
                    <?php if (isset($error_msg) && $error_msg != ""): ?>
                        <div class="alert alert-danger" onclick="this.remove()" role="alert"><?= $error_msg ?></div>
                    <?php endif; ?>

                    <?php if (isset($success_msg) && $success_msg != ""): ?>
                        <div class="alert alert-success" onclick="this.remove()" role="alert"><?= $success_msg ?></div>
                    <?php endif; ?>

                       <div id="error_div" class="alert alert-danger" style="display:none" onclick="this.style.display='none'" role="alert"></div>

                        <div class="card">
                            <div class="card-header">Sign in</div>
                            <div class="card-body">
                                <form id="signin_form">
                                    <div class="form-group row">
                                        <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input name="email" type="email" id="email" class="form-control" placeholder="user@example.com" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                        <div class="col-md-6">
                                            <input name="pwd" type="password" id="pwd" class="form-control" placeholder="••••••" minlength="2"  required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                        <a href="/recover" class="btn btn-link">
                                            Forgot Your Password?
                                        </a>
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

        <script src="js/pages/signin.js"></script>
        <script src="js/url.js"></script>

    </body>
</html>