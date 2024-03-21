<?php

    class FileSysHandler
    {
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
                    
                    if (is_file($file_path)) 
                    {
                        $file_count++;
                    }
                }
        
                return $file_count;
            } 
            else 
            {
                return -1; 
            }
        }

        public static function deleteDir($dir) 
        {   
            if (!is_dir($dir)) {
                return;
            }

            $dir_handle = opendir($dir);
        
            if (!$dir_handle) {
                return;
            }
        
            while (($entry = readdir($dir_handle)) !== false) {
                if ($entry != "." && $entry != "..") {
                    $entry_path = $dir . DIRECTORY_SEPARATOR . $entry;
        
                    if (is_dir($entry_path)) {
                        self::deleteDir($entry_path);
                    } else {
                        unlink($entry_path);
                    }
                }
            }
        
            closedir($dir_handle);
        
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