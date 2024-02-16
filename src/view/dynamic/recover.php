<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupero Account</title>
    <!-- Includi jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link a SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <style>

    </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
            <div id="input_error" style="display:none" class="alert alert-danger" onclick="this.style.display='none'" role="alert"></div>
                <div class="card">

                    <div class="card-body" id="email_box" style="display:block">
                    
                        <h1 class="card-title text-center mb-4">Inserisci la mail</h1>
                        
                        <form id="email_form">
                            <div class="mb-3">
                                <input id="id_email" name="email" class="form-control" type="email" required>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary" type="button" onclick="validateEmail()">Continua</button>
                            </div>
                        </form>

                    </div>
                    <div class="card-body" id="recoverykey_box" style="display:none">
                        
                        <h1 class="card-title text-center mb-4">Inserisci la chiave di recupero</h1>
                        
                        <form id="recoverykey_form">
                            <div class="mb-3">
                                <input id="id_recoverykey" name="recoverykey" class="form-control" type="password" required>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary" type="button" onclick="validateRecoverykey()">Conferma</button>
                            </div>
                        </form>

                    </div>



                    <div class="card-body" id="password_box" style="display:none">
                        
                        <h1 class="card-title text-center mb-4">Inserisci la nuova password</h1>
                        
                        <form>
                            <div class="mb-3">
                                <input id="id_pwd1" name="pwd1" class="form-control" type="password" required>
                            </div>
                            <div class="mb-3">
                                <input id="id_pwd2" name="pwd2" class="form-control" type="password" required>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary" type="button" onclick="validatePassword()">Conferma</button>
                            </div>

                        </form>

                    </div>


                    <div class="card-body" id="success_box" style="display:none">
                        
                        <h1 class="card-title text-center mb-4">Password reimposta correttamente</h1>
                        <form>
                            <div class="text-center">
                                <button class="btn btn-primary" type="button" onclick="redirectSignin()">Ok</button>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="script.js"></script>
</body>

<script>
    const DOMAIN = '<?php echo $_ENV['DOMAIN']; ?>'
</script>

</html>
