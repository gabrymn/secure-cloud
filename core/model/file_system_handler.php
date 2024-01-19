<?php

    class file_system_handler
    {
        public static function mk_dir($user_email, $dir)
        {
            if (!is_dir($dir))
                mkdir($dir);

            $dir_user = md5("xdir" . $user_email);

            if (is_dir($dir . $dir_user))
                return false;
            else
                return mkdir($dir . $dir_user);
        }
    }


?>