<?php

    require_once 'request.php';

    function send_email($t, $sbjct, $msg)
    {

        $sbjct = base64_encode($sbjct);
        $msg = base64_encode($msg);

        $url = "https://mywebs.altervista.org/mail.php?TO={$t}&SUB={$sbjct}&MSG={$msg}";
        
        try {
            $r = new ARequest($url);
            $r->send();
        }catch(Exception $e){
            return 0;
        }

        return 1;
    }

?>