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

        public function __construct($port = 465)
        {
            try {

                $this->mailer = new PHPMailer(true);
                $this->mailer->isSMTP();
                $this->mailer->Host = $_ENV['EMAIL_HOST'];
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $_ENV['EMAIL_USERNAME'];
                $this->mailer->Password = $_ENV['EMAIL_PASSWORD'];
                $this->mailer->SMTPSecure = 'ssl';
                $this->mailer->Port = $port;
                $this->mailer->setFrom($_ENV['EMAIL_USERNAME']);
                
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

                //return $this->mailer->ErrorInfo;
                return false;
            }
        }

        public function send_verify_email()
        {
            
        }

        public static function is_real($email)
        {
            return true;
        }
    }

?>