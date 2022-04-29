<?php

    require_once '../resources/api.php';

	if (isset($_COOKIE['PHPSESSID']))
	{
		session_start();
		setcookie ('PHPSESSID', "", time() - 3600, "/");
		session_destroy();
		session_write_close();
        if (isset($_COOKIE['logged']) && isset($_COOKIE['rm_tkn']))
        {
            sqlc::connect();
            sqlc::rem_del(hash("sha256", $_COOKIE['rm_tkn']));
            setcookie ('logged', false, time() - 3600, "/");
		    setcookie ('rm_tkn', false, time() - 3600, "/");
        }
	}

	header("Location: login");

?>