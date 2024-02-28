<?php

    require_once __DIR__ . '/../model/userSecurity.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/fileTransfer.php';
    require_once __DIR__ . '/../controller/userKeys.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/storage/fileSysHandler.php';
    require_once __DIR__ . '/../../resource/security/cryptoRNDString.php';
    require_once __DIR__ . '/../../resource/security/userKeysHandler.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../../resource/myDateTime.php';
    require_once __DIR__ . '/../view/assets/navbar.php';

    class CloudDriveController
    {
        public static function renderClouddrivePage()
        {
            //$file_names = FileController::get_file_names_of($_SESSION['ID_USER']);
            
            $navbar = Navbar::getPrivate('clouddrive');

            include __DIR__ . '/../view/clouddrive.php';
        }

        public static function renderFilePreview()
        {
            
        }

        public static function getFileNamesOf($id_user)
        {
            $key = "KEY_HERE";

            $file_names = FileModel::selFileNamesBy_UserID($id_user);

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
    }

?>