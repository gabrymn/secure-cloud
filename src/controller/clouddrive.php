<?php

    require_once __DIR__ . '/../model/user_security.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../view/assets/navbar.php';

    class CloudDriveController
    {
        public static function render_clouddrive_page()
        {
            $dkey = $_SESSION['DKEY'];

            $user = new User(id_user: $_SESSION['ID_USER']);

            $user->sel_email_from_id();

            $us = new UserSecurity(id_user: $user->get_id_user());

            $rkey = $us->sel_rkey_from_id();

            $navbar = Navbar::getPrivate('clouddrive');
            include __DIR__ . '/../view/clouddrive.php';
        }

        public static function upload_files($files)
        {
            $user = new User(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $us = new UserSecurity(id_user: $user->get_id_user());

            $ckey_c = $us->sel_ckey_from_id();
            $rkey_c = $us->sel_rkey_from_id();

            $rkey = crypto::decrypt($rkey_c, $_SESSION['DKEY']);

            $ckey = crypto::decrypt($ckey_c, $rkey);

            $root_dir = FileSysHandler::get_user_storage_dir($user->get_id_user(), $user->get_email());
            
            foreach ($files as $file)
            {
                $file_name_encrypted = crypto::encrypt($file['name'], $ckey, crypto::HEX);

                $full_path = $root_dir . '/' .  $file_name_encrypted;   

                crypto::encryptFile($file['tmp_name'], $full_path, $ckey);
                unlink($file['tmp_name']);
            }

            http_response::successful(200);
        }
    }

?>