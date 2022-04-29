<?php
    
    class system 
    {
        // 6000 => 1h
        private const ONE_DAY_SQL = 1440000;
        // 3600 => 1h
        private const ONE_DAY_COOKIE = 86400;

        // default remember for 20 days
        public static function remember($id_user, int $days = 20)
        {
            if (!isset($_COOKIE['ALLOW'])) return false;

            $t_sql = self::ONE_DAY_SQL * $days;
            $t_cookie = self::ONE_DAY_COOKIE * $days;

            sqlc::connect();

            $tkn = new token(36, "", "", array("A-Z", "a-z", "0-9"));
            $state = sqlc::rem_ins(hash("sha256", $tkn->val()), $id_user, $t_sql);
            
            if ($state){
                setcookie('logged', 1, time() + $t_cookie, "/");
                setcookie('rm_tkn', $tkn->val(), time() + $t_cookie, "/");
                return true;
            }
            else return false;
        }

        public static function redirect_priv_area($id_user, $session_data = array()){

            session_start();
            $_SESSION['ID_USER'] = $id_user;

            if (count($session_data) > 0)
                foreach ($session_data as $key => $value)
                    $_SESSION[$key] = $value;

            header("Location: private");
        }
    }

?>