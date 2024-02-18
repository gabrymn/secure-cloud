<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/two_factor_auth.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/token.php';
    require_once __DIR__ . '/../../resource/client.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/user_security.php';
    
    class OTPController
    {
        public static function render_auth2_page()
        {
            include __DIR__ . '/../view/auth2.php';
        }

        private static function check_otp_format($otp)
        {   
            if (!preg_match('/^\d{6}$/', $otp) === 1)
                http_response::client_error(400, "Invalid OTP format"); 
        }

        public static function processOtpChecking($otp)
        {
            self::check_otp_format($otp);

            $user = new User(id: $_SESSION['ID_USER']);
            $user->set_email($user->sel_email_from_id());

            $us = new UserSecurity(id_user:$user->get_id());
            $us->sel_rkey_from_id();
            $us->sel_secret_2fa_c_from_id();
            
            $rkey = crypto::decrypt_AES_GCM($us->get_rkey_encrypted(), $_SESSION['DKEY']);

            $secret_2fa = crypto::decrypt_AES_GCM($us->get_secret_2fa_encrypted(), $rkey);

            $tfa = new MyTFA(email: $user->get_email(), secret: $secret_2fa);

            if ($tfa->codeIsValid($otp) === false)
                http_response::client_error(400, "OTP code is wrong");
            
            $_SESSION['AUTH_2FA'] = true;
            $_SESSION['LOGGED'] = true;

            unset($_SESSION['OTP_CHECKING']);

            Session::create_or_load($user->get_id(), client::get_ip());
            
            http_response::successful
            (
                200, 
                false, 
                array("redirect" => '/clouddrive')
            );
        }
    }



?>