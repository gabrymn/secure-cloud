<?php

    require_once __ROOT__ . 'model/http/http_response.php';

    function handle_get(&$title, &$subtitle, &$redirect)
    {
        session_start();

        if (!isset($_SESSION['EMAIL_VERIFING'])){
            header("location:signin");
            exit;
        }

        $title = "Ti abbiamo inviato un email di verifica, controlla la tua casella di posta";
        $subtitle = "Clicca per continuare";
        $redirect = 'signin';
        
        /*
        if (isset($_GET['first']) && count($_GET) === 1)
        {
            $t = "Verifica email";

            if ($_GET['first'] == 1)
            {
                $p = "Ti abbiamo inviato un email di verifica, controlla la tua casella di posta";
                $b = "Clicca per continuare";
                $r = 'signin.php';
            }
            else
            {   
                $p = "Verifica il tuo account prima di poter accedere. <br><br>Non hai ricevuto la nostra mail?";
                $b = "Clicca qui";
                $r = 'verify.php?';
            }
        }
        else
        {
            if (isset($_COOKIE['PHPSESSID']))
            {
                session_start();
                $email = $_SESSION['EMAIL'];
                send_email_verification($email, "verify.php?first=1");
            }
            else header("Location: signin.php");
        }*/
    }

?>