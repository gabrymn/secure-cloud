<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    
    class AccountVerifyController
    {
        public static function render_verify_page()
        {
            $title = "";
            $subtitle1 = "";
            $subtitle2 = "";
            $redirect = "";

            switch($_SESSION['VERIFY_PAGE_STATUS'])
            {
                case 'SIGNIN_WITH_EMAIL_NOT_VERIFIED':
                {
                    $title = "Procedura di verifica email";
                    $subtitle1 = "Prima di poter accedere devi verificare l'email, non l'hai ricevuta?";
                    $subtitle2 = "Clicca qui";
                    $redirect = '/sendverifyemail';

                    break;
                }

                case 'VERIFY_EMAIL_SENT':
                {
                    $title = "Procedura di verifica email";
                    $subtitle1 = "Ti abbiamo inviato nuovamente un'email di verifica, clicca il link che ti abbiamo inviato per verificare l'account";
                    $subtitle2 = "Accedi";
                    $redirect = '/signin';
                    unset($_SESSION['VERIFY_PAGE_STATUS']);
                    session_destroy();

                    break;
                }

                default:
                {
                    http_response::redirect('/signin');
                    unset($_SESSION['VERIFY_PAGE_STATUS']);
                    session_destroy();

                    break;
                }

            }
    
            include __DIR__ . '/../view/verify.php';
        }

        public static function send_verify_email()
        {
            $user = new User(email: $_SESSION['EMAIL']);
            $user->set_id($user->sel_id_from_email());

            $tkn = new token(100);
            $everify = new EmailVerify(tkn_hash:$tkn->hashed(), id_user:$user->get_id());
            $everify->ins();

            if (MyMail::send_email_verify($user->get_email(), (string)$tkn))
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT';
                unset($_SESSION['EMAIL']);
                
                http_response::redirect('/verify');
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
            
            $redirect = '/signin';
    
            return 
            [
                "success_msg" => $success_msg, 
                "error_msg"  => $error_msg,
                "redirect" => $redirect
            ];
        }
    }



?>