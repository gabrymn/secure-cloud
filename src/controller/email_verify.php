<?php

    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../view/assets/navbar.php';

    class EmailVerifyController
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
                    $redirect = '/verify/sendemail';

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
                    unset($_SESSION['VERIFY_PAGE_STATUS']);
                    session_destroy();
                    http_response::redirect('/signin');

                    break;
                }
            }
            
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/verify.php';
        }

        public static function send_email_verify_from_signin()
        {
            $email = $_SESSION['EMAIL'];

            if (self::send_email_verify($email))
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT';
                unset($_SESSION['EMAIL']);
                
                http_response::redirect('/verify');
            }
            else
            {
                http_response::server_error(500);
            }
        }

        public static function send_email_verify($email)
        {
            $user = new UserModel(email: $email);
            $user->sel_id_from_email();

            $ev_token = EmailVerifyModel::generate_token();

            $ev = new EmailVerifyModel
            (
                token_hash: hash("sha256", $ev_token), 
                id_user: $user->get_id_user()
            );

            mypdo::begin_transaction();

            if (!$ev->ins())
            {
                mypdo::roll_back();
                return false;
            }

            $mail_header = $ev->get_mail_header();
            $mail_body = $ev->get_mail_body($ev_token);

            $mymail = new MyMail();

            if (!$mymail->send_array(array_merge($mail_header, $mail_body)))
            {
                mypdo::roll_back();
                return false;
            }

            mypdo::commit();
            return true;
        }

        public static function check_email_verify_token($token)
        {
            $success_msg = "";
            $error_msg = "";
    
            $ev = new EmailVerifyModel
            (
                token_hash: hash("sha256", $token)
            );
    
            switch ($ev->sel_id_user_from_token_hash())
            {
                case false:
                {
                    http_response_code(500);
                    $error_msg = "Internal Server Error";
                    break;
                }

                case -1:
                {
                    http_response_code(400);
                    $error_msg = "Invalid or expired email verify link.";
                    break;
                }

                default:
                {
                    if (session_status() !== PHP_SESSION_ACTIVE)
                    session_start();
    
                    if (isset($_SESSION['VERIFING_EMAIL']))
                        unset($_SESSION['VERIFING_EMAIL']);
        
                    $user = new UserModel(id_user: $ev->get_id_user());

                    $user->upd_user_to_verified();
                    $ev->del_from_token_hash();
                    
                    $success_msg = "Email verified, sign in";
                    break;
                }
            }

            return 
            [
                "success_msg" => $success_msg, 
                "error_msg"  => $error_msg
            ];
        }
    }



?>