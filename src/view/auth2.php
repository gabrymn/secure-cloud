<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci il Codice OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <link href=img/favicon.svg rel="icon" type="image/x-icon">
    <link href="css/shared.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
</head>
<body>

    <?php echo $navbar; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

            <div id="error_box" style="display:none" onclick="this.style.display='none'" class="alert alert-danger" role="alert">Errore</div>

                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Inserisci il Codice OTP</h1>
                        <form id="otp_form">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Codice OTP:</label>
                                <input maxlength="6" id="otp" name="otp" class="form-control" pattern="\d{6}" title="Inserisci un codice OTP a 6 cifre" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Verifica Codice</button>
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
    <script src="js/url.js"></script>
    <script src="js/pages/otp.js"></script>
</body>
</html>