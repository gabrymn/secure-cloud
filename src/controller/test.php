<?php

    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../model/model.php';
    
    class TestController
    {
        public static function process_test()
        {
            $filename = "ciao.env";

            FileSysHandler::rm_dir(__DIR__ . '/../../users_storage');

            echo self::handle($filename);
        }

        public static function handle($filename)
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