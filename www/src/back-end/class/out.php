<?php

    require_once "sqlc.php";
	
	if (isset($_COOKIE['PHPSESSID']))
	{
		session_start();
		
		$session_sc_id = $_SESSION['SESSION_SC_ID'];

		sqlc::connect();
		sqlc::upd_session($session_sc_id);
		sqlc::expire_session($session_sc_id);

		unset($_SESSION['SESSION_SC_ID']);
		unset($_SESSION['SESSION_STATUS_ACTIVE']);

		setcookie('PHPSESSID', "", time() - 3600, "/");
		session_destroy();
		session_write_close();
        if (isset($_COOKIE['rm_tkn']))
        {
            sqlc::rem_del(hash("sha256", $_COOKIE['rm_tkn']));
		    setcookie('rm_tkn', false, time() - 3600, "/");
        }
	}

	header("Location: ../../front-end/public/log.php");
	exit;
?>