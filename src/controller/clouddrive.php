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

            $files = FileModel::sel_fileIDs_fileNames_by_userID($id_user);

            foreach ($files as &$file)
            {
                $file['fullpath'] = Crypto::decrypt($file['fullpath_encrypted'], $cipherkey);
                unset($file['fullpath_encrypted']);

                /*if (in_array($file_name, array_column($file_names, 'fullpath_encrypted')))
                {
                    FileSysHandler::handleFilenameExists($file_name);
                }*/
            }

            return $files;
        }
    }

?>