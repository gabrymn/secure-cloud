<?php

    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../../resource/security/my_two_factor_auth.php';
    
    class ProfileController
    {
        public static function render_profile_page()
        {
            $navbar = Navbar::getPrivate('profile');

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $us = new UserSecurityModel(id_user: $user->get_id_user());

            $us->sel_secret_2fa_c_from_id();
            $us->sel_rkey_from_id();
            $us->sel_ckey_from_id();

            $rkey_c = $us->get_rkey_encrypted();
            $ckey_c = $us->get_ckey_encrypted();

            $rkey = crypto::decrypt($rkey_c, $_SESSION['DKEY']);

            $ckey = crypto::decrypt($ckey_c, $rkey);

            $secret_2fa = crypto::decrypt($us->get_secret_2fa_encrypted(), $rkey);

            $tfa = new MyTFA($user->get_email(), $secret_2fa);

            $qrcode_url = $tfa->get_qrcode_url();

            include __DIR__ . '/../view/profile.php';
        }
    }

?>