<?php

    require_once __DIR__ . '/../../model/user_security.php';
    require_once __DIR__ . '/../../model/user.php';
    require_once __DIR__ . '/../../model/file.php';
    require_once __DIR__ . '/../../model/file_transfer.php';
    require_once __DIR__ . '/../../../resource/security/crypto.php';
    require_once __DIR__ . '/../../../resource/http/http_response.php';
    require_once __DIR__ . '/../../../resource/storage/file_system_handler.php';
    require_once __DIR__ . '/../../../resource/security/crypto_rnd_string.php';
    require_once __DIR__ . '/../../../resource/security/user_keys_handler.php';
    require_once __DIR__ . '/../../../resource/mydatetime.php';
    require_once __DIR__ . '/../../view/assets/navbar.php';

    class CloudDriveController
    {
        public static function renderClouddrivePage()
        {
            //$file_names = FileController::get_file_names_of($_SESSION['ID_USER']);
            
            $navbar = Navbar::getPrivate('clouddrive');

            include __DIR__ . '/../../view/clouddrive.php';
        }

        public static function renderFilePreview()
        {
            
        }

        public static function getFileNamesOf($id_user)
        {
            $key = "KEY_HERE";

            $file_names = FileModel::selFileNamesFromUserID($id_user);

            for($i=0; $i<count($file_names); $i++)
            {
                $file_name = Crypto::decrypt($file_names[$i], $key);

                if (in_array($file_name, $file_names))
                {
                    FileSysHandler::handleFilenameExists($file_name);
                }

                $file_names[$i] = $file_name;
            }

            return $file_names;
        }

        public static function handleDownloadOf($id_file)
        {
            $file_path = __DIR__ . "/../../../storage/9cc95054e5baf13ea974cc30e58e36b6/secure-cloud-main (1).zip"; // from id_file get file_path
            httpResponse::download($file_path);
        }
    }

?>