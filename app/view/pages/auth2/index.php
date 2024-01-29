<?php

    define('__ROOT__', '../../../'); 
    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/http_response.php';
    require_once __ROOT__ . 'model/ds/google2FA.php';
    require_once __ROOT__ . 'model/ds/qry.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'GET': {
                session_start();

                if (!isset($_SESSION['OTP_CHECKING']))
                {
                    http_response::client_error(401);
                }
                break;
            }

            default: {
                http_response::client_error(405);
            }
        }
    }
    else http_response::server_error(500);
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci il Codice OTP</title>
    <!-- Link a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link a SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">

</head>
<body>

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

    <script>

        $('#otp_form').on('submit', async (e) => {

            e.preventDefault();

            var formData = new FormData(document.getElementById('otp_form'));

            const url = 'http://localhost/api/otp.php';
            const method = 'POST';

            try {
                const response = await fetch(url, 
                {
                    method: method,
                    body: formData,
                });

                if (response.ok)
                {
                    // test
                    //console.log(await response.text());
                    //return false;
                    
                    const json = await response.json();
                    window.location.href = json.redirect;
                }
                else
                {
                    const errorTxt = await response.text();
                    const errorJson = JSON.parse(errorTxt);
                    $('#error_box').css("display", "block");
                    $('#error_box').html(errorJson.status_message);
                }

            } catch (error) {
                console.log(error)
                $('#error_box').css("display", "block");
                $('#error_box').html("There was a problem, try again");
            }
        });

    </script>

</body>
</html>