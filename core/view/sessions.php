
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Secure Cloud</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href=img/favicon.svg rel="icon" type="image/x-icon">
        <link href="css/shared.css" rel="stylesheet">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    </head>
    <body>


    <?php echo $navbar; ?>
    
    <br><br>

    <table class="table table-dark tbls custom-table" id="ID_SESSIONS">
        <thead>
            <tr class="chd">
                <th scope="col">OS</th>
                <th scope="col">Browser</th>
                <th scope="col">IP Address</th>
                <th scope="col">Location</th>
                <th scope="col">Last Activity</th>
                <th scope="col">Status</th>
                <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody id="ID_TBL_BODY">
            
            <?php echo $sessions_view; ?>

        </tbody>
    </table>
    <br><br>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
    <script src="js/pages/sessions.js"></script>
    <script src="js/protected.js"></script>
</body>

<script>
    const CURRENT_SESSION_TOKEN = '<?php echo $_SESSION['SESSION_TOKEN']; ?>';

    $(document).ready(() => {
        setInterval(checkSessionStatus, <?php echo SessionController::SESSION_STATUS_CHECK_MS; ?>);
    })

</script>

</html>

