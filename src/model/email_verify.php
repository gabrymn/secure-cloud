<?php

    require_once __DIR__ . '/model.php';

    class EmailVerifyModel extends Model
    {
        private string $token_hash;
        private string $token_plain_text;
        private int $id_user;
        private $expires;
        private string $email;


        public const PLAIN_TEXT_TOKEN_LEN = 100;
        private const EXP_MINUTES = 30;
        public const HASH_ALGO = "sha256";

        public function __construct($token_hash = null, $token_plain_text = null, $id_user = null, $expires = null, $email = null)
        {
            date_default_timezone_set(self::TZ);
            
            if ($token_plain_text !== null)
                self::setTokenPlainText($token_plain_text);
            else
            {
                if ($token_hash === null)
                    self::setTokenPlainTextRandom();
                else
                {
                    self::setTokenPlainText(self::DEFAULT_STR);
                    self::setTokenHash($token_hash);
                }
            }

            ($token_plain_text ? $token_plain_text : parent::DEFAULT_STR);
            self::setTokenHash($token_hash ? $token_hash : parent::DEFAULT_STR);

            self::setExpires($expires ? $expires : $this->setExpiresAuto());
            self::setUserID($id_user ? $id_user : parent::DEFAULT_INT);
            self::setEmail($email ? $email : parent::DEFAULT_STR);

            $this->token_plain_text = parent::DEFAULT_STR;
        }

        public function getMailHeader() : array
        {
            return
            [
                "dest" => $this->getEmail(),
                "obj" => $_ENV['APP_NAME'] . ': verify your email',
            ];
        }

        public function getMailBody() : array
        {
            $url = $_ENV['APP_URL'] . '/signin?token=' . $this->token_plain_text;

            return 
            [
                "body" => 'Click the link to confirm your email: ' . $url
            ];
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function getTokenHash()
        {
            return $this->token_hash;
        }

        public function getUserID()
        {
            return $this->id_user;
        }

        public function getExpires()
        {
            return $this->expires;
        }

        public function setTokenPlainText($token_plain_text)
        {
            $this->token_plain_text = $token_plain_text;

            if ($this->token_plain_text !== parent::DEFAULT_STR)
            {
                $token_hash = hash(self::HASH_ALGO, $this->token_plain_text);
                $this->setTokenHash($token_hash);
            }
        }

        public function setTokenPlainTextRandom()
        {
            $token_plain_text = $this->generateUID(self::PLAIN_TEXT_TOKEN_LEN);
            $this->setTokenPlainText($token_plain_text);
        }

        public function setTokenHash($token_hash)
        {
            $this->token_hash = $token_hash;
        }

        public function setUserID($id_user)
        {
            $this->id_user = $id_user;
        }

        public function setExpires(string $expires)
        {
            $this->expires = $expires;
        }

        public function setExpiresAuto()
        {
            $expires = date(parent::DATE_FORMAT, strtotime("+" . strval(self::EXP_MINUTES) . " minutes", time()));
            $this->expires = $expires;
        }

        public function checkExpires() : bool
        {
            $expires = new DateTime(self::getExpires());
            $now = new DateTime(date(parent::DATE_FORMAT));

            return $expires < $now;
        }

        public function toAssocArray($token_hash = false, $expires = false, $email = false, $id_user = false)
        {
            $params = array();

            if ($token_hash)
                $params['token_hash'] =  $this->getTokenHash();

            if ($expires)
                $params['expires'] =  $this->getExpires();

            if ($email)
                $params['email'] =  $this->getEmail();

            if ($id_user)
                $params['id_user'] =  $this->getUserID();

            return $params;
        }

        /**
         *   query insert email_verify record 
        */
        public function ins($email_insert = false) : bool
        {
            $qry = "INSERT INTO email_verify (token_hash, expires, id_user) VALUES (:token_hash, :expires, :id_user)";

            MyPDO::connect(MyPDO::EDIT);

            return MyPDO::qryExec
            (
                $qry, 
                $this->toAssocArray(token_hash:true, expires:true, email:$email_insert, id_user:true)
            );
        }

        public function sel_userID_by_tokenHash()
        {
            $qry = "SELECT id_user FROM email_verify WHERE token_hash = :token_hash";

            MyPDO::connect(MyPDO::SELECT);

            $res = MyPDO::qryExec($qry, $this->toAssocArray(token_hash:true));

            if ($res === false)
                return false;
            else if ($res === array())
                return -1;
            else
            {
                $id_user = intval($res[0]['id_user']);
                $this->setUserID($id_user);
                return $this->getUserID();
            }
        }

        public function del_by_tokenHash()
        {
            $qry = "DELETE FROM email_verify WHERE token_hash = :token_hash OR id_user = :id_user";

            MyPDO::connect(MyPDO::EDIT);

            return MyPDO::qryExec($qry, $this->toAssocArray(token_hash:true, id_user:true));
        }
    }

?>