
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
                <th scope="col">Recent Activity</th>
                <th scope="col">Status</th>
                <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody id="ID_TBL_BODY">
            
            <!-- this PHP add all the sessions related to the current user -->
            <?php

                $sessions = Session::get_sessions_of($_SESSION['ID_USER'], $_SESSION['CURRENT_ID_SESSION']);
                
                echo "<script>let sessionRefs = new Map();</script>";

                $i = 0;
                foreach ($sessions as $session)
                {
                    $loc = $session['city'] . ', ' . $session['country'];
                    
                    unset($session['city']);
                    unset($session['country']);

                    echo "<tr>";
                    echo "<td>{$session['os']}</td>";
                    echo "<td>{$session['browser']}</td>";
                    echo "<td>{$session['ip']}</td>";
                    echo "<td>{$loc}</td>";
                    echo "<td>{$session['recent_activity']}</td>";
                    echo "<td id=id_sess_status_$i>{$session['status']}</td>";
                    
                    if ($session['status'] !== 'Expired')
                        echo 
                        "<td id=id_td_expire_{$i}>
                            <button onclick=expireSession({$i}) class='close-sess-btn'>Close</button>
                        </td>";

                    echo "</tr>";

                    echo 
                    "<script>
                        sessionRefs.set('{$i}', '{$session['id_session']}')
                    </script>";

                    $i++;
                } 

            ?>

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
    const CURRENT_ID_SESSION = '<?php echo $_SESSION['CURRENT_ID_SESSION']; ?>';

    $(document).ready(() => {
        setInterval(checkSessionStatus, 488888000);
    })

</script>

</html>

