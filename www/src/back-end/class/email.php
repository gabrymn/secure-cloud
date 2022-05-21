<?php

    require_once 'request.php';

    function send_email($t, $sbjct, $msg, $red = "signin.php")
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

    function email_is_real($email)
    {
        $api_url = "https://emailverification.whoisxmlapi.com/api/v2?apiKey=at_edHQLKARdq9wMLzBCmeneWc10Y33S&emailAddress={$email}";
        $r = new ARequest($api_url);
        $array = json_decode($r->send(), true);
        return 
        (
            isset($array['smtpCheck']) && isset($array['dnsCheck']) ? 
                $array['smtpCheck'] || $array['dnsCheck'] : 0
        );
    }

?>