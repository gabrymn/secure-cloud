<?php

    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../controller/userKeys.php';
    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/storage/fileSysHandler.php';
    require_once __DIR__ . '/../../resource/security/userKeysHandler.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../view/assets/fileicons.php';

    class CloudDriveController
    {
        public static function renderClouddrivePage()
        {
            $filenames = self::getFileNamesOf($_SESSION['ID_USER']);
            
            $clouddrive_icons = FileIcons::get($filenames);
            $navbar = Navbar::getPrivate('clouddrive');

            include __DIR__ . '/../view/clouddrive.php';
        }

        public static function renderFilePreview()
        {
            
        }

        public static function getFileNamesOf($id_user)
        {
            $cipherkey = UserKeysController::getCipherKey();

            $file_names = FileModel::sel_fileNames_by_userID($id_user);

            for($i=0; $i<count($file_names); $i++)
            {
                $file_name = Crypto::decrypt($file_names[$i], $cipherkey);

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