<?php

    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/security/cryptoRNDString.php';
    require_once __DIR__ . '/../view/assets/navbar.php';

    class EmailVerifyController
    {
        public static function renderVerifyPage()
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
                    httpResponse::redirect('/signin');

                    break;
                }
            }
            
            $navbar = Navbar::getPublic();
            include __DIR__ . '/../view/verify.php';
        }

        public static function sendEmailVerifyFromSignin()
        {
            $email = $_SESSION['EMAIL'];

            if (self::sendEmailVerify($email))
            {
                $_SESSION['VERIFY_PAGE_STATUS'] = 'VERIFY_EMAIL_SENT';
                unset($_SESSION['EMAIL']);
                
                httpResponse::redirect('/verify');
            }
            else
            {
                httpResponse::serverError(500);
            }
        }

        public static function sendEmailVerify($email)
        {
            $user = new UserModel(email: $email);
            $user->selIDByEmail();

            $ev = new EmailVerifyModel
            (
                id_user: $user->getUserID()
            );

            MyPDO::beginTransaction();

            if (!$ev->ins())
            {
                MyPDO::rollBack();
                return false;
            }

            $mail_header = $ev->getMailHeader();
            $mail_body = $ev->getMailBody();

            $mymail = new MyMail();

            if (!$mymail->sendArray(array_merge($mail_header, $mail_body)))
            {
                MyPDO::rollBack();
                return false;
            }

            MyPDO::commit();
            return true;
        }

        public static function checkEmailVerifyToken($token)
        {
            $success_msg = "";
            $error_msg = "";
    
            $ev = new EmailVerifyModel
            (
                token_plain_text: $token
            );
    
            switch ($ev->sel_userID_by_tokenHash())
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
        
                    $user = new UserModel(id_user: $ev->getUserID());

                    $user->updUserToVerified();
                    $ev->del_by_tokenHash();
                    
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