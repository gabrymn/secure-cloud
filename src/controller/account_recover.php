<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user_security.php';

    require_once __DIR__ . '/../../resource/http/http_response.php';
    require_once __DIR__ . '/../../resource/security/user_keys_handler.php';


    class AccountRecoveryController
    {
        public static function render_recover_page()
        {
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/recover.php';
        }

        public static function process_rkey_check($email, $rkey)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                http_response::client_error(400, "Invalid email format");

            $user = new UserModel(email:$email);
            $user->sel_id_from_email($user->get_email());
            
            $us = new UserSecurityModel();
    
            $us->sel_rkey_hash_from_email
            (
                $user->to_assoc_array(email:true)
            );

            if (!password_verify($rkey, $us->get_rkey_hash()))
                http_response::client_error(400, "The provided recovery key is incorrect. Please double-check the key and try again.");
            
            session_start();
            
            $_SESSION['RECOVERING_ACCOUNT'] = true;
            $_SESSION['ID_USER'] = $user->get_id_user();
            $_SESSION['RKEY'] = $rkey;
    
            http_response::successful(200);
        }

        public static function process_pwd_reset($pwd)
        {
            if (strlen($pwd) < 2)
                http_response::client_error(400, "Invalid password format");

            $ukh = new UserKeysHandler();
            
            $ukh->set_pwd($pwd);
            $ukh->set_dkey_salt_random();
            $ukh->set_dkey_auto();
            $ukh->set_rkey($_SESSION['RKEY']);

            $us = new UserSecurityModel
            (
                pwd_hash:       $ukh->get_pwd_hashed(),
                rkey_encrypted: $ukh->get_rkey_encrypted(),
                dkey_salt:      $ukh->get_dkey_salt(),
                id_user:        $_SESSION['ID_USER']
            );

            $status = $us->upd_pwdhash_rkeyc_dkeysalt_from_iduser();

            session_destroy();
            
            if ($status === false)
                http_response::server_error();

            http_response::successful();
        }
    }

?>