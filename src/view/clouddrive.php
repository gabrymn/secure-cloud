<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Secure Cloud</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href=img/favicon.svg rel="icon" type="image/x-icon">
        <link href="css/shared.css" rel="stylesheet">
        <link href="css/pages/clouddrive.css" rel="stylesheet">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    </head>
    <body>
    
        <?php echo $navbar; ?>

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

        <div class="uploader"><button id="ID_UPLOAD" type="button" class="btn btn-primary btn-lg btn-block">Upload File <i class="fa fa-cloud-upload" aria-hidden="true"></i></i></button></div>

        <br><br>
        <h1 id="ID_NFS" class="nfs" style="display:none">...<h1>

        <div id="C_LOADING" style="display:none;margin-left:auto;margin-right:auto" class="lds-dual-ring"></div>

        <div id="CONT_FILES" class="container" style="display:none">
            <div id="C_FILES" class="row">
            </div>
        </div>

        <input type="file" id="ID_FILE_UPLOADER" style="display:none" multiple>

        <br><br><br><br><br>

        <table border="1">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><i class="fas fa-folder"></i></td>
                    <td>Directory 1</td>
                    <td>Directory</td>
                </tr>
                <tr>
                    <td><i class="fas fa-file"></i></td>
                    <td>File 1.txt</td>
                    <td>File</td>
                </tr>
                <tr>
                    <td><i class="fas fa-file"></i></td>
                    <td>File 2.png</td>
                    <td>File</td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>


        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script type="module" src="js/pages/clouddrive.js"></script>
        <script src="js/protected.js"></script>

    </body> 

    <script>
    
        $(document).ready(() => {
           // setInterval(checkSessionStatus, <?php echo SessionController::SESSION_STATUS_CHECK_MS; ?>);
        })

    </script>

</html>



