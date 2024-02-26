<?php

    require_once __DIR__ . '/../security/cryptoRNDString.php';
    require_once __DIR__ . '/../storage/fileSysHandler.php';

    class UploadSession extends DataStructure
    {
        private const UPLOAD_SESSION_ID_LEN = 32;
        public const UPLOAD_CHUNK_SIZE = 1000000;
        private const STORAGE_ROOT_DIR = __DIR__ . '/../../storage';

        public static string $upload_session_id = "";

        public static function initialize($space_required)
        {
            if (!isset($_SESSION['upload_sessions']))
                $_SESSION['upload_sessions'] = [];

            $upload_session_id = (new CryptoRNDString())->generate(self::UPLOAD_SESSION_ID_LEN);
            self::setID($upload_session_id);

            $_SESSION['upload_sessions'][self::$upload_session_id] = [];
            $_SESSION['upload_sessions'][self::$upload_session_id]['upload_space_used'] = 0;
            $_SESSION['upload_sessions'][self::$upload_session_id]['upload_space_limit'] = $space_required;

            self::createSessionDir();
        }

        public static function setID($upload_session_id)
        {
            self::$upload_session_id = $upload_session_id;
        }

        public static function getID()
        {
            return self::$upload_session_id;
        }

        public static function checkID($upload_session_id) : bool
        {
            return 
            (
                in_array
                (
                    $upload_session_id, 
                    array_keys($_SESSION['upload_sessions'])
                )
            );
        }

        private static function getSpaceUsed()
        {
            return intval($_SESSION['upload_sessions'][self::getID()]['upload_space_used']);
        }

        private static function getSpaceLimit()
        {
            return intval($_SESSION['upload_sessions'][self::getID()]['upload_space_limit']);
        }

        public static function checkUsedSpace($chunk_size)
        {
            $space_used = self::getSpaceUsed();
            $space_limit = self::getSpaceLimit();
            $chunk_size = intval($chunk_size);

            if ($space_used + $chunk_size > $space_limit)
                return false;
            else
                return true;
        }

        public static function increaseUsedSpace($chunk_size)
        {
            $_SESSION['upload_sessions'][self::getID()]['upload_space_used'] += $chunk_size; 
        }

        public static function createSessionDir()
        {
            $upload_session_dir = self::STORAGE_ROOT_DIR . '/';
            $upload_session_dir .= $_SESSION['USER_DIR'] . '/';
            $upload_session_dir .= FileSysHandler::UPLOADS_DIRNAME . '/';
            $upload_session_dir .= self::getID();

            if (!is_dir($upload_session_dir))
                mkdir($upload_session_dir);

            $_SESSION['upload_sessions'][self::getID()]['buffer_dir'] = $upload_session_dir;
        }

        public static function getSessionDir()
        {
            return $_SESSION['upload_sessions'][self::getID()]['buffer_dir'];
        }

        public static function getChunkDir($filename)
        {
            return self::getSessionDir() . '/' . $filename;
        }

        public static function getFinalDir()
        {
            $final_dir = self::STORAGE_ROOT_DIR . '/';
            $final_dir .= $_SESSION['USER_DIR'] . '/';
            $final_dir .= FileSysHandler::DATA_DIRNAME;

            return  $final_dir;
        }

        public static function uploadIsCompleted()
        {
            $space_used = self::getSpaceUsed();
            $space_limit = self::getSpaceLimit();

            return ($space_used === $space_limit);
        }

        public static function destroy()
        {
            FileSysHandler::deleteDir(self::getSessionDir());
            unset($_SESSION['upload_sessions'][self::getID()]);
        }

        public static function fileUploadIsCompleted($filename, $chunks_len)
        {
            $chunk_dir = self::getChunkDir($filename);

            return 
            (
                FileSysHandler::countFilesOf($chunk_dir) === intval($chunks_len)
            );
        }
    }

?>