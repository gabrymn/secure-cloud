<?php

    require_once 'token.php';
    require_once 'request.php';
    require_once 'email.php';
    require_once 'sqlc.php';
    require_once "dmn.php";
    
    if (!defined('DMN')) define('DMN', get_dmn()); 

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

            sqlc::connect("USER_STD_INS");
            $tkn = new token(36, "", "", array("a-z", "0-9"));
            $state = sqlc::rem_ins(hash("sha256", $tkn->val()), $id_user, $t_sql);
            sqlc::close();

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

            sqlc::connect("USER_STD_SEL");

            $email = sqlc::get_email($id_user);
            sqlc::close();
            $sub = "Verifica OTP";
            $msg = "OTP code: " . $otp->val();
            
            if (send_email($email, $sub, $msg, "otp.php"))
                header("Location: otp.php");
            else
                header("Location: signin.php");
            exit;
        }

        public static function redirect_remember($rm_token){
            $tkn = $rm_token;
            $htkn = hash("sha256", $tkn);
            sqlc::connect("USER_STD_SEL");
            $data = sqlc::rem_sel($htkn);
            sqlc::close();
            session_start();
            $_SESSION['ID_USER'] = $data['id_user'];
            $_SESSION['AUTH'] = 0;
            header("Location: ../private/cloud.php");
        }
        
        public static function mk_dir($email, $dir)
        {
            sqlc::connect("USER_STD_SEL");
            $id_user = sqlc::get_id_user($email);
            sqlc::close();
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
            
            sqlc::connect("USER_STD_SEL");
            $id_user = sqlc::get_id_user($email);
            sqlc::close();
            sqlc::connect("USER_STD_INS");
            sqlc::ins_tkn_verify(intval($id_user), $token->hashed());
            sqlc::close();

            $sub = "Secure-cloud: verify your email";
            $link = DMN."/secure-cloud/www/src/front-end/public/signin.php?";
            $link .= "tkn={$token->val()}";
            $msg = "Click this link: $link";

            send_email($email, $sub, $msg, $red);
        }
    }

?>