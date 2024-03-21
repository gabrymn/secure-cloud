<?php

    require __DIR__ . '/../vendor/autoload.php';
    
    class MyMail
    {
        private \PHPMailer\PHPMailer\PHPMailer $mailer;

        public function __construct($username, $password, $email_host, $port = 465)
        {
            try {
                $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
                //$this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
                $this->mailer->isSMTP();
                $this->mailer->Host = $email_host;
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $username;
                $this->mailer->Password = $password;
                $this->mailer->SMTPSecure = 'ssl';
                $this->mailer->Port = $port;
                $this->mailer->setFrom($username);
                //$this->mailer->Timeout = 5;
                
            } catch (\PHPMailer\PHPMailer\Exception $e) {

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

            } catch (\PHPMailer\PHPMailer\Exception $e) {

                //return $this->mailer->ErrorInfo;
                return false;
            }
        }

        public function sendArray(array $mail, $html = true)
        {
            try {
                
                return $this->mailer->send($mail["dest"], $mail["obj"], $mail["body"]);

            } catch (\PHPMailer\PHPMailer\Exception $e) {

                //return $this->mailer->ErrorInfo;
                return false;
            }
        }
    }

?>