<?php

    require_once __DIR__ . '/../model/user_security.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/file_transfer.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mydatetime.php';
    require_once __DIR__ . '/../view/assets/navbar.php';

    class CloudDriveController
    {
        public static function render_clouddrive_page()
        {
            $dkey = $_SESSION['DKEY'];

            $user = new User(id_user: $_SESSION['ID_USER']);

            $user->sel_email_from_id();

            $us = new UserSecurity(id_user: $user->get_id_user());

            $rkey_c = $us->sel_rkey_from_id();
            $ckey_c = $us->sel_ckey_from_id();

            $rkey = crypto::decrypt($rkey_c, $_SESSION['DKEY']);

            $ckey = crypto::decrypt($ckey_c, $rkey);

            $navbar = Navbar::getPrivate('clouddrive');

            $file_names = File::sel_file_names_from_id_user($user->get_id_user());

            foreach ($file_names as &$file_name)
            {
                $file_name = crypto::decrypt($file_name, $ckey);
            }

            include __DIR__ . '/../view/clouddrive.php';
        }

        public static function upload_files($files_php)
        {
            $user = new User(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $us = new UserSecurity(id_user: $user->get_id_user());

            $ckey_c = $us->sel_ckey_from_id();
            $rkey_c = $us->sel_rkey_from_id();

            $rkey = crypto::decrypt($rkey_c, $_SESSION['DKEY']);

            $ckey = crypto::decrypt($ckey_c, $rkey);

            $root_dir = FileSysHandler::get_user_storage_dir($user->get_id_user(), $user->get_email());

            $rnd_str = new CryptoRNDString();

            foreach ($files_php as $file_php)
            {
                $id_file = $rnd_str->generate(File::ID_FILE_LEN);

                $file_name_encrypted = crypto::encrypt($file_php['name'], $ckey, crypto::HEX);

                mypdo::connect('insert');
                mypdo::begin_transaction();

                $file = new File
                (
                    id_file:$id_file, 
                    full_path: $file_name_encrypted, 
                    size: $file_php['size'], 
                    mime_type: $file_php['type'], 
                    id_user: $user->get_id_user()
                );

                $file_transfer = new FileTransfer
                (
                    transfer_type: "upload",
                    id_file: $id_file
                );

                if ($file->ins() && $file_transfer->ins())
                {
                    mypdo::commit();
                }
                else
                {
                    mypdo::roll_back();
                }

                $full_path = $root_dir . '/' .  $file_name_encrypted;   

                crypto::encryptFile($file_php['tmp_name'], $full_path, $ckey);
                unlink($file_php['tmp_name']);
            }

            http_response::successful(200);
        }
    }

?>