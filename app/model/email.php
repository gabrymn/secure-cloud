<?php

    require_once 'http/http_request.php';

    class email 
    {
        public static function is_real($email)
        {
            return true;
        }

        public static function send($to, $sbjct, $msg)
        {
            $to = base64_encode($to);
            $sbjct = base64_encode($sbjct);
            $msg = base64_encode($msg);
            $apikey = 'ZjljYTNiNmU4ZDE0YWI5YzNkNWU=';
            
            $url = "https://mywebs.altervista.org/mail/api/mailsender.php?TO={$to}&SUB={$sbjct}&MSG={$msg}&apiKey={$apikey}";
            
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