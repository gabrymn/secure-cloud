<?php

    class FileSysHandler
    {
        private const ROOT_STORAGE_DIR =  __DIR__ . '/../../storage';
        public const DATA_DIRNAME =  'data';
        public const UPLOADS_DIRNAME =  '.uploads_buffer';
        public const DOWNLOADS_DIRNAME =  '.downloads_buffer';

        public static function get_user_dir($id_user, $email_user)
        {
            return md5($id_user . $email_user);
        }

        public static function mk_user_dir($id_user, $email_user)
        {
            if (!is_dir(self::ROOT_STORAGE_DIR))
                mkdir(self::ROOT_STORAGE_DIR);

            $user_dir_root = self::ROOT_STORAGE_DIR . '/' . md5($id_user . $email_user);

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

        public static function count_files_of($dir)
        {
            $dir .= "/";            
            $fileCount = 0;
        
            if (is_dir($dir)) 
            {
                $files = scandir($dir);
        
                foreach ($files as $file) 
                {
                    $filePath = $dir . $file;
                    
                    // Escludi le directory e i collegamenti simbolici
                    if (is_file($filePath)) 
                    {
                        $fileCount++;
                    }
                }
        
                return $fileCount;
            } 
            else 
            {
                return -1; // Codice di errore: directory non esistente
            }
        }

        public static function rm_dir($dir) 
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
                        self::rm_dir($entry_path);
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

        public static function handle_filename_exists(string &$filename)
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