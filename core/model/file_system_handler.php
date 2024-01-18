<?php

    class file_system_handler
    {
        public static function mk_dir($user_email, $dir)
        {
            sqlc::connect("USER_STD_SEL");
            $id_user = sqlc::get_id_user($user_email);
            sqlc::close();
            $dir_user = md5("dir" . $id_user . $user_email);
            return mkdir($dir . "users/" . $dir_user);
        }
    }


?>