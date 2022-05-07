<?php

    require_once 'token.php';
    require_once 'request.php';
    require_once 'email.php';
    require_once 'sqlc.php';

    class system 
    {
        // 6000 => 1h
        private const ONE_DAY_SQL = 1440000;
        // 3600 => 1h
        private const ONE_DAY_COOKIE = 86400;

        // default remember for 20 days
        public static function remember($id_user, int $days = 20)
        {
            $t_sql = self::ONE_DAY_SQL * $days;
            $t_cookie = self::ONE_DAY_COOKIE * $days;

            sqlc::connect();

            $tkn = new token(36, "", "", array("a-z", "0-9"));
            $state = sqlc::rem_ins(hash("sha256", $tkn->val()), $id_user, $t_sql);
            
            if ($state){
                setcookie('rm_tkn', $tkn->val(), time() + $t_cookie, "/");
                return true;
            }
            else return false;
        }

        public static function redirect_otp_form($id_user)
        {
            session_start();
            $otp = new token("OTP");
            $_SESSION['HOTP'] = array("value" => $otp->hashed(), "exp" => time() + 60*5);

            sqlc::connect();

            $email = sqlc::get_email($id_user);
            $sub = "Verifica OTP";
            $msg = "OTP code: " . $otp->val();
            
            if (send_email($email, $sub, $msg, "otp-form.php"))
                header("Location: otp-form.php");
            else
                header("Location: log.php");

            exit;
        }

        public static function redirect_remember($rm_token){
            $tkn = $rm_token;
            $htkn = hash("sha256", $tkn);
            sqlc::connect();
            $data = sqlc::rem_sel($htkn);
            session_start();
            $_SESSION['ID_USER'] = $data['id_user'];
            $_SESSION['AUTH'] = 0;
            header("Location: pvt.php");
        }
        
        public static function mk_dir($email, $dir)
        {
            sqlc::connect();
            $id_user = sqlc::get_id_user($email);
            $email_user = $email;
            $dir_user = md5("dir" . $id_user . $email_user);
            return mkdir($dir . "users/" . $dir_user);
        }

        public static function verify($email, $first)
        {
            if ($first) self::send_email_verification($email, "verify.php?first=$first");
            else header("Location: verify.php?first=0");
        }

        public static function send_email_verification($email, $red)
        {
            $token = new token(15, "", "", array("a-z", "0-9"));

            sqlc::connect();
            $id_user = sqlc::get_id_user($email);
            sqlc::ins_tkn_verify(intval($id_user), $token->hashed());
        
            $sub = "Secure-cloud: verify your email";
            $link = "http://127.0.0.1/secure-cloud/www/src/front-end/public/log.php?";
            $link .= "tkn={$token->val()}";
            $msg = "Click this link: $link";

            send_email($email, $sub, $msg, $red);
        }
    }

?>