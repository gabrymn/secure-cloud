<?php

    class FileSysHandler
    {
        private const ROOT_STORAGE_DIR =  __DIR__ . '/../../storage';
        public const DATA_DIRNAME =  'data';
        public const UPLOADS_DIRNAME =  '.uploads_buffer';
        public const DOWNLOADS_DIRNAME =  '.downloads_buffer';
        private const HASH_ALGO = "sha256";

        public static function getUserDirName($id_user, $email_user)
        {
            return hash(self::HASH_ALGO, ($id_user . $email_user));
        }

        /**
         * Creates a directory structure for a user in the storage system.
         *
         * This function creates a directory structure for a user, including the main storage directory
         * and a subdir for the user data and temp dirs for uploads, and downloads. 
         * The main directory is created by hashing the userID and email using SHA-256.
         *
         * @param int    $id_user     Unique ID of the user.
         * @param string $email_user  User's email address.
         *
         * @return bool Returns true if the directory structure is successfully created,
         *              false on failure.
         *
         * @throws Exception If any issues arise during directory creation.
         */
        public static function makeUserDir($id_user, $email_user)
        {
            if (!is_dir(self::ROOT_STORAGE_DIR))
                mkdir(self::ROOT_STORAGE_DIR);

            $user_dir_root = self::ROOT_STORAGE_DIR . '/' . self::getUserDirName($id_user, $email_user);

            $user_dir_data = $user_dir_root . '/' . self::DATA_DIRNAME;
            $user_dir_uploads = $user_dir_root . '/' . self::UPLOADS_DIRNAME;
            $user_dir_downloads = $user_dir_root . '/' . self::DOWNLOADS_DIRNAME;

            return
            (
                mkdir($user_dir_root) &&
                mkdir($user_dir_data) &&
                mkdir($user_dir_uploads) &&
                mkdir($user_dir_downloads)
            );
        }

        public static function countFilesOf($dir)
        {
            $dir .= "/";            
            $file_count = 0;
        
            if (is_dir($dir)) 
            {
                $files = scandir($dir);
        
                foreach ($files as $file) 
                {
                    $file_path = $dir . $file;
                    
                    // Escludi le directory e i collegamenti simbolici
                    if (is_file($file_path)) 
                    {
                        $file_count++;
                    }
                }
        
                return $file_count;
            } 
            else 
            {
                return -1; // Codice di errore: directory non esistente
            }
        }

        public static function deleteDir($dir) 
        {
            if (!is_dir($dir)) {
                // Non è una directory, quindi non c'è nulla da eliminare
                return;
            }
        
            // Apre la directory
            $dir_handle = opendir($dir);
        
            if (!$dir_handle) {
                // Non è possibile aprire la directory
                return;
            }
        
            // Itera sui contenuti della directory
            while (($entry = readdir($dir_handle)) !== false) {
                if ($entry != "." && $entry != "..") {
                    $entry_path = $dir . DIRECTORY_SEPARATOR . $entry;
        
                    // Se è una directory, richiama la funzione ricorsivamente
                    if (is_dir($entry_path)) {
                        self::deleteDir($entry_path);
                    } else {
                        // Se è un file, elimina il file
                        unlink($entry_path);
                    }
                }
            }
        
            // Chiude la directory
            closedir($dir_handle);
        
            // Elimina la directory stessa
            rmdir($dir);
        }

        public static function handleFilenameExists(string &$filename)
        {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);

            $filename = "";

            if ($name)
            {
                $filename .= $name;
                $filename .= '_';
                $filename .= uniqid();
    
                if ($ext)
                    $filename .= '.'.$ext;
            }
            else
            {
                $filename .= '.'.$ext;
                $filename .= '_';
                $filename .= uniqid();
            }

            return $filename;
        }
    }


?>