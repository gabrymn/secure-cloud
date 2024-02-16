<?php

    class file_system_handler
    {
        public static function mk_user_storage_dir($user_email, $dir_path)
        {
            if (!is_dir($dir_path))
                mkdir($dir_path);

            $dir_user = md5($user_email);

//           if (is_dir($dir_path . $dir_user))
//               return false;
//           else
                
            return mkdir($dir_path . $dir_user);
        }

        function rm_dir($dir_path) 
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