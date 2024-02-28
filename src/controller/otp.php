<?php

    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/security/myTFA.php';
    require_once __DIR__ . '/../../resource/storage/myPDO.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../../resource/http/client.php';
    require_once __DIR__ . '/../model/session.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/userSecurity.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    
    class OTPController
    {
        public static function renderAuth2Page()
        {
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/auth2.php';
        }

        private static function checkOTPFormat($otp)
        {   
            if (!preg_match('/^\d{6}$/', $otp) === 1)
                httpResponse::clientError(400, "Invalid OTP format"); 
        }

        public static function processOTPChecking($otp)
        {
            self::checkOTPFormat($otp);

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->setEmail($user->selEmailByID());

            $us = new UserSecurityModel(id_user:$user->getUserID());
            $us->sel_rKeyEnc_by_userID();
            $us->sel_secret2faEnc_by_userID();
            
            $rkey = Crypto::decrypt($us->getRecoveryKeyEncrypted(), $_SESSION['MASTER_KEY']);

            $secret_2fa = Crypto::decrypt($us->getSecret2faEncrypted(), $rkey);

            $tfa = new MyTFA(email: $user->getEmail(), secret: $secret_2fa);

            if ($tfa->codeIsValid($otp) === false)
                httpResponse::clientError(400, "OTP code is wrong");
            
            $_SESSION['AUTH_2FA'] = true;
            unset($_SESSION['OTP_CHECKING']);

            SessionController::initSession(Client::getIP(), $user->getUserID());

            httpResponse::successful
            (
                200, 
                false, 
                array("redirect" => '/clouddrive')
            );
        }
    }



?>