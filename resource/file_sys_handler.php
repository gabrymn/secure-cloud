<?php

    class FileSysHandler
    {
        private const ROOT_STORAGE_DIR =  __DIR__ . '/../users_storage';

        public static function get_user_storage_dir($id_user, $email_user)
        {
            return self::ROOT_STORAGE_DIR . '/' . md5($id_user . $email_user);
        }

        public static function mk_user_storage_dir($id_user, $email_user)
        {
            if (!is_dir(self::ROOT_STORAGE_DIR))
                mkdir(self::ROOT_STORAGE_DIR);

            $user_dir = self::ROOT_STORAGE_DIR . '/' . md5($id_user . $email_user);

            return mkdir($user_dir);
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

        public static function rm_dir($dir_path) 
        {
            if (!is_dir($dir_path)) {
                // Non è una directory, quindi non c'è nulla da eliminare
                return;
            }
        
            // Apre la directory
            $dir_handle = opendir($dir_path);
        
            if (!$dir_handle) {
                // Non è possibile aprire la directory
                return;
            }
        
            // Itera sui contenuti della directory
            while (($entry = readdir($dir_handle)) !== false) {
                if ($entry != "." && $entry != "..") {
                    $entry_path = $dir_path . DIRECTORY_SEPARATOR . $entry;
        
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
            rmdir($dir_path);
        }
    }


?>