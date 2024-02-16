<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    
    class AccountVerifyController
    {
        public static function render_verify_page()
        {
            session_start();

            $title = "";
            $subtitle1 = "";
            $subtitle2 = "";
            $redirect = "";
    
            if (!isset($_SESSION['VERIFY_PAGE_STATUS']))
            {
                http_response::redirect($_ENV['DOMAIN'] . '/');
            }
    
            if ($_SESSION['VERIFY_PAGE_STATUS'] === 'SIGNUP_OK')
            {
                $title = "Procedura di verifica email";
                $subtitle1 = "Registrazione avvenuta con successo";
                $subtitle2 = "Clicca qui";
                $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';
            }
            else if ($_SESSION['VERIFY_PAGE_STATUS'] === 'SIGNIN_WITH_EMAIL_NOT_VERIFIED')
            {
                $title = "Procedura di verifica email";
                $subtitle1 = "Prima di poter accedere devi verificare l'email, non l'hai ricevuta?";
                $subtitle2 = "Clicca qui";
                $redirect = $_ENV['DOMAIN'] . '/api/send_verify_email.php';
            }
            else if ($_SESSION['VERIFY_PAGE_STATUS'] === 'VERIFY_EMAIL_SENT_NF')
            {
                $title = "Procedura di verifica email";
                $subtitle1 = "Ti abbiamo inviato nuovamente un'email di verifica, clicca il link che ti abbiamo inviato e potrai accedere";
                $subtitle2 = "Ok";
                $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';
                unset($_SESSION['VERIFY_PAGE_STATUS']);
                session_destroy();
            }
            else
            {
                $title = "Errore!";
                $subtitle1 = "Non sei autizzato a visualizzare questa pagina";
                $subtitle2 = "Home page";
                $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';
                unset($_SESSION['VERIFY_PAGE_STATUS']);
                session_destroy();
            }

            include __DIR__ . '/../view/dynamic/verify.php';
        }

        public static function send_verify_email()
        {
            session_start();

            if (!(isset($_SESSION['EMAIL']) && isset($_SESSION['VERIFY_PAGE_STATUS'])))
            {
                session_destroy();
                http_response::client_error(401);
            }

            if ($_SESSION['VERIFY_PAGE_STATUS'] !== 'SIGNIN_WITH_EMAIL_NOT_VERIFIED')
            {
                session_destroy();
                http_response::client_error(401);
            }

            $user = new User(email: $_SESSION['EMAIL']);
            $user->set_id($user->sel_id_from_email());

            $tkn = new token(100);
            $everify = new EmailVerify(tkn_hash:$tkn->hashed(), id_user:$user->get_id());
            $everify->ins();

            if (MyMail::send_email_verify($user->get_email(), (string)$tkn))
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT_NF';
                unset($_SESSION['EMAIL']);

                http_response::redirect($_ENV['DOMAIN'] . '/verify');
            }
            else
                http_response::server_error(500);
        }

        public static function check_email_verify_token($token)
        {
            $success_msg = "";
            $error_msg = "";
            $redirect = "";
    
            $tkn = new Token;
            $tkn->set($token);
    
            $e_verify = new EmailVerify(tkn_hash: $tkn->hashed());
    
            $id_user = $e_verify->sel_id_from_tkn();
    
            if ($id_user === -1)
            {
                http_response_code(400);
                $error_msg = "Invalid or expired email verify link.";
            }
            else
            {
                if (!session_status()) 
                    session_start();
    
                if (isset($_SESSION['VERIFING_EMAIL']))
                    unset($_SESSION['VERIFING_EMAIL']);
    
                $user = new User(id: $id_user);
                $user->upd_user_verified();
    
                $e_verify->del_ver_from_tkn();
    
                $success_msg = "Email verified, sign in";
            }
            
            $redirect = $_ENV['DOMAIN'] . '/view/pages/signin/index.php';
    
            return 
            [
                "success_msg" => $success_msg, 
                "error_msg"  => $error_msg,
                "redirect" => $redirect
            ];
        }
    }



?>