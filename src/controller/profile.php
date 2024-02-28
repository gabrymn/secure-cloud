<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../../resource/security/myTFA.php';
    
    class ProfileController
    {
        public static function renderProfilePage()
        {
            $navbar = Navbar::getPrivate('profile');
            
            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->selEmailByID();

            $us = new UserSecurityModel(id_user: $user->getUserID());

            $us->sel_secret2faEnc_by_userID();
            $us->sel_rKeyEnc_by_userID();
            $us->sel_cKeyEnc_by_userID();

            $rkey_encrypted = $us->getRecoveryKeyEncrypted();
            $ckey_encrypted = $us->getCipherKeyEncrypted();

            $rkey = Crypto::decrypt($rkey_encrypted, $_SESSION['MASTER_KEY']);

            $ckey = Crypto::decrypt($ckey_encrypted, $rkey);

            $secret_2fa = Crypto::decrypt($us->getSecret2faEncrypted(), $rkey);

            $tfa = new MyTFA($user->getEmail(), $secret_2fa);

            $qrcode_url = $tfa->getQrcodeURL();

            include __DIR__ . '/../view/profile.php';
        }
    }

?>