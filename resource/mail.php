<?php

    require __DIR__ . '/../vendor/autoload.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    use React\Promise\Promise;
    
    class MyMail
    {
        private PHPMailer $mailer;

        public function __construct($port = 465)
        {
            try {
                $this->mailer = new PHPMailer(true);
                //$this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
                $this->mailer->isSMTP();
                $this->mailer->Host = $_ENV['EMAIL_HOST'];
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $_ENV['EMAIL_USERNAME'];
                $this->mailer->Password = $_ENV['EMAIL_PASSWORD'];
                $this->mailer->SMTPSecure = 'ssl';
                $this->mailer->Port = $port;
                $this->mailer->setFrom($_ENV['EMAIL_USERNAME']);
                //$this->mailer->Timeout = 5;
                
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

        private function sendAsync($dest, $obj, $body, $html = true): Promise
        {
            $promise = new Promise(function ($resolve, $reject) use ($dest, $obj, $body, $html)
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
            });

            return $promise;
        }


        public static function send_email_verify($email, $tkn_plain_txt)
        {
            $mailer = new MyMail();

            $url = $_ENV['DOMAIN'] . '/view/pages/signin/index.php?tkn=' . $tkn_plain_txt;
            $body = 'Click the link to confirm your email: ' . $url;
            $obj = 'secure-cloud: verify your email';

            return $mailer->send($email, $obj, $body);  
        }
    }

?>