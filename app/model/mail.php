<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    //use PHPMailer\PHPMailer\SMTP;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    class MyMail
    {
        private PHPMailer $mailer;

        public function __construct($email_username, $email_password, $email_host, $port = 465)
        {
            try {

                $this->mailer = new PHPMailer(true);
                $this->mailer->isSMTP();
                $this->mailer->Host = $email_host;
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $email_username;
                $this->mailer->Password = $email_password;
                $this->mailer->SMTPSecure = 'ssl';
                $this->mailer->Port = $port;
                $this->mailer->setFrom($email_host);
                
                return true;

            } catch (Exception $e) {

                return $this->mailer->ErrorInfo;
            }
        }

        public function send($dest, $obj, $body, $html = true)
        {
            try {
                
                $this->mailer->addAddress($dest);
                $this->mailer->isHTML($html);  
                $this->mailer->Subject = $obj;                          
                $this->mailer->Body = $body;
                return $this->mailer->send();

            } catch (Exception $e) {

                return $this->mailer->ErrorInfo;
            }
        }

        public static function is_real($email)
        {
            return true;
        }

        public static function get_confirm_email_body($domain, $msg, $tkn)
        {
            $url = $domain . $tkn;
            $body =  $msg . ": " . $url;
            return $body;
        }
    }

?>