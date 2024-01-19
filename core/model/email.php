<?php

    require_once 'http/http_request.php';

    class email 
    {
        public static function is_real($email)
        {
            return true;
        }

        public static function send($t, $sbjct, $msg, $red = "signin.php")
        {
            $sbjct = base64_encode($sbjct);
            $msg = base64_encode($msg);
            $red = base64_encode($red);
            
            $url = "https://mywebs.altervista.org/mail.php?TO={$t}&SUB={$sbjct}&MSG={$msg}&REDIRECT={$red}";
            header("Location: $url");exit;
            
            try {
                $r = new ARequest($url);
                var_dump($r->send());
            }catch(Exception $e){
                return 0;
            }
    
            return 1;
        }
    }

?>