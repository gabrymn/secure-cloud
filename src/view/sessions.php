
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SECURE CLOUD</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href="css/shared.css" rel="stylesheet">
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="border-bottom:1px solid white">
            <div class="container-fluid">
                <a class="navbar-brand" href="cloud.php">CLOUD</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="storage.php">Storage</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="transfers.php">Transfers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page" href="sessions.php">Sessions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="settings.php">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" style="font-weight:900" aria-current="page"></a>
                        </li>
                        <button onclick="window.location.href='../../back-end/class/out.php'" class="btn btn-dark btn-secondary">
                          <i class="fa fa-sign-out"></i>
                          <span>Logout</span>
                        </button>
                    </ul>
                </div>
            </div>
        </nav>

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


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/sha256.js"></script>
        <script src="script.js"></script>
    </body>

    <script>
        const CURRENT_ID_SESSION = '<?php echo $_SESSION['CURRENT_ID_SESSION']; ?>';
    </script>

</html>

