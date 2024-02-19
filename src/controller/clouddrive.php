<?php

    require_once __DIR__ . '/../model/user_security.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../view/assets/navbar.php';


    class CloudDriveController
    {
        public static function render_clouddrive_page()
        {
            $dkey = $_SESSION['DKEY'];

            $user = new User(id: $_SESSION['ID_USER']);

            $us = new UserSecurity(id_user: $user->get_id());

            $rkey = $us->sel_rkey_from_id();

            $navbar = Navbar::getPrivate('clouddrive');
            include __DIR__ . '/../view/clouddrive.php';
        }
    }

?>