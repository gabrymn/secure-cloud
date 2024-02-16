<?php

    define('__QP__', __ROOT__ . 'sql_qrys/');

    require_once __ROOT__ . 'model/ds/functions.php';
    require_once __ROOT__ . 'model/models/email_verify.php';
    require_once __ROOT__ . 'model/models/user.php';
    require_once __ROOT__ . 'model/ds/mypdo.php';
    require_once __ROOT__ . 'model/ds/token.php';

    function check_email_verify_token($token)
    {
        $success_msg = "";
        $error_msg = "";
        $redirect = "";

        $tkn = new Token;
        $tkn->set($token);

        $e_verify = new EmailVerify(tkn_hash: $tkn->hashed());

        $id_user = $e_verify->sel_id_from_tkn();

        if ($id_user === -1)
        {
            http_response_code(400);
            $error_msg = "Invalid or expired email verify link.";
        }
        else
        {
            if (!session_status()) 
                session_start();

            if (isset($_SESSION['VERIFING_EMAIL']))
                unset($_SESSION['VERIFING_EMAIL']);

            $user = new User(id: $id_user);
            $user->upd_user_verified();

            $e_verify->del_ver_from_tkn();

            $success_msg = "Email verified, sign in";
        }
        
        $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';

        return 
        [
            "success_msg" => $success_msg, 
            "error_msg"  => $error_msg,
            "redirect" => $redirect
        ];
    }


?>