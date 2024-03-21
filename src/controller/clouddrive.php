<?php

    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../controller/user_keys.php';
    require_once __DIR__ . '/../../utils/httpkit/http_response.php';
    require_once __DIR__ . '/../../utils/file_sys_handler.php';
    require_once __DIR__ . '/../../utils/securekit/user_keys_handler.php';
    require_once __DIR__ . '/../../utils/securekit/crypto.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/../view/assets/file_icons_view.php';

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