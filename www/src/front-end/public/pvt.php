<?php

    echo "private area";exit;

	if (isset($_COOKIE['PHPSESSID']))
	{
		session_start();
        if (isset($_SESSION['ID_USER']))
        {
            sqlc::connect();
            $email = sqlc::get_email($_SESSION['ID_USER']);
            echo "<h1>Private area of: [ $email ]</h1><br>";
            echo "<h3><a href='out'>logout</a></h3>";
        }
        else
            response::client_error(403);
	}
    else if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn'])){
        if ($_COOKIE['logged']){
            $tkn = $_COOKIE['rm_tkn'];
            $htkn = hash("sha256", $tkn);
            sqlc::connect();
            $data = sqlc::rem_sel($htkn);
            if ($data) system::redirect_priv_area($data['id_user']);
        }
    }
	else
        response::client_error(403);
?>