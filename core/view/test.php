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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="../img/icon.svg" rel="icon" type="image/x-icon" >
    </head>
    <body>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
            <h3 class="modal-title" id="ID_MODAL_TITLE">Filename</h3>
            <button id="ID_MODAL_CLOSE" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body" id="ID_MODAL_BODY">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-success" id="ID_MODAL_SHOW">SHOW</button>
            <button type="button" class="btn btn-primary" id="ID_MODAL_DOWNLOAD">DOWNLOAD</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal" id="ID_MODAL_DEL">DELETE</button>
            </div>
            </div>
            </div>
        </div>

        <br><br><br>

        <br><br>
        <h1 id="ID_NFS" class="nfs" style="display:none">...<h1>

        <div id="C_LOADING" style="display:none;margin-left:auto;margin-right:auto" class="lds-dual-ring"></div>

        <div id="CONT_FILES" class="container" style="display:none">
            <div id="C_FILES" class="row">
            </div>
        </div>

        <input type="file" id="ID_FILE_UPLOADER" style="display:none" multiple>

        <br><br><br><br><br>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="../js/login.js"></script>
        <script src="../js/shared.js"></script>
    </body>
</html>

<style>

    input {
        color: white;
        border: 2px solid white;
        outline: none;
    }

    .uploader {

        width: 70%;
        margin-left: auto;
        margin-right: auto;
    }

    #CONT_FILES {
        width: 90%;
        margin-left: auto;
        margin-right: auto;
        border-radius: 25px;
        padding-top: 50px;
        padding-bottom: 50px;
        background-color: rgb(90,90,90);
    }

    .cardmy {
        margin: 20px;
        width: 200px;
    }

    .nfs {
        color: white;
        font-size: 4rem;
        font-weight: 100;
        text-align: center;
    }

    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }

    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #fff;
        border-color: #fff transparent #fff transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }


</style>