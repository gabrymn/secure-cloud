<?php

    require_once __ROOT__ . 'model/http/http_response.php';

    function handle_get(&$title, &$subtitle1, &$subtitle2, &$redirect)
    {
        session_start();

        if (!isset($_SESSION['VERIFY_PAGE_STATUS'])){
            header("location:" . $_ENV['DOMAIN'] . '/view/signin/signin.php');
            exit;
        }

        if ($_SESSION['VERIFY_PAGE_STATUS'] === 'SIGNUP_OK')
        {
            $title = "Procedura di verifica email";
            $subtitle1 = "Registrazione avvenuta con successo";
            $subtitle2 = "Clicca qui";
            $redirect = $_ENV['DOMAIN'] . '/view/signin/signin.php';
        }
        else if ($_SESSION['VERIFY_PAGE_STATUS'] === 'SIGNIN_WITH_EMAIL_NOT_VERIFIED')
        {
            $title = "Procedura di verifica email";
            $subtitle1 = "Prima di poter accedere devi verificare l'email, non l'hai ricevuta?";
            $subtitle2 = "Clicca qui";
            $redirect = $_ENV['DOMAIN'] . '/api/confirm_email/main.php';
        }
        else if ($_SESSION['VERIFY_PAGE_STATUS'] === 'VERIFY_EMAIL_SENT_NF')
        {
            $title = "Procedura di verifica email";
            $subtitle1 = "Ti abbiamo inviato nuovamente un'email di verifica, clicca il link che ti abbiamo inviato e potrai accedere";
            $subtitle2 = "Ok";
            $redirect = $_ENV['DOMAIN'] . '/view/signin/signin.php';
            unset($_SESSION['VERIFY_PAGE_STATUS']);
            session_destroy();
        }
        else
        {
            $title = "Errore!";
            $subtitle1 = "Non sei autizzato a visualizzare questa pagina";
            $subtitle2 = "Home page";
            $redirect = $_ENV['DOMAIN'] . '/api/confirm_email/main.php';
            unset($_SESSION['VERIFY_PAGE_STATUS']);
            session_destroy();
        }
    }

?>