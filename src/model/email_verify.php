<?php

    require_once __DIR__ . '/model.php';

    class EmailVerify extends Model
    {
        private string $token_hash;
        private int $id_user;
        private $expires;
        private string $email;

        public const PLAIN_TEXT_TOKEN_LEN = 100;
        private const EXP_MINUTES = 30;

        public function __construct($token_hash = null, $id_user = null, $expires = null, $email = null)
        {
            date_default_timezone_set(self::TZ);
            self::set_token_hash($token_hash ? $token_hash : parent::DEFAULT_STR);
            self::set_expires($expires ? $expires : $this->set_expires());
            self::set_id_user($id_user ? $id_user : parent::DEFAULT_INT);
            self::set_email($email ? $email : parent::DEFAULT_STR);
        }

        public static function generate_token() : string
        {
            $token_plain_text = parent::generate_uid(self::PLAIN_TEXT_TOKEN_LEN);
            return $token_plain_text;
        }

        public function get_mail_header() : array
        {
            return
            [
                "dest" => $this->get_email(),
                "obj" => $_ENV['APP_NAME'] . ': verify your email',
            ];
        }

        public function get_mail_body($token_plain_text) : array
        {
            $url = $_ENV['APP_URL'] . '/signin?token=' . $token_plain_text;

            return 
            [
                "body" => 'Click the link to confirm your email: ' . $url
            ];
        }

        public function set_email($email)
        {
            $this->email = $email;
        }

        public function get_email()
        {
            return $this->email;
        }

        public function get_token_hash()
        {
            return $this->token_hash;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function get_expires()
        {
            return $this->expires;
        }

        public function set_token_hash($token_hash)
        {
            $this->token_hash = $token_hash;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function set_expires($expires = false)
        {
            if ($expires === false)
                $expires = date(parent::DATE_FORMAT, strtotime("+" . strval(self::EXP_MINUTES) . " minutes", time()));
            
            $this->expires = $expires;
        }

        public function check_expires() : bool
        {
            $expires = new DateTime(self::get_expires());
            $now = new DateTime(date(parent::DATE_FORMAT));

            return $expires < $now;
        }

        public function to_assoc_array($token_hash = false, $expires = false, $email = false, $id_user = false)
        {
            $params = array();

            if ($token_hash)
                $params['token_hash'] =  $this->get_token_hash();

            if ($expires)
                $params['expires'] =  $this->get_expires();

            if ($email)
                $params['email'] =  $this->get_email();

            if ($id_user)
                $params['id_user'] =  $this->get_id_user();

            return $params;
        }

        /**
         *   query insert email_verify record 
        */
        public function ins($email_insert = false) : bool
        {
            $qry = "INSERT INTO email_verify (token_hash, expires, id_user) VALUES (:token_hash, :expires, :id_user)";

            mypdo::connect('insert');

            return mypdo::qry_exec
            (
                $qry, 
                $this->to_assoc_array(token_hash:true, expires:true, email:$email_insert, id_user:true)
            );
        }

        public function sel_id_user_from_token_hash()
        {
            $qry = "SELECT id_user FROM email_verify WHERE token_hash = :token_hash";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(token_hash:true));

            switch ($res)
            {
                case false:
                    return false;
                case array():
                    return -1;
                default:
                {
                    $id_user = intval($res[0]['id_user']);
                    $this->set_id_user($id_user);
                    return $this->get_id_user();
                }
            }
        }

        public function del_from_token_hash()
        {
            $qry = "DELETE FROM email_verify WHERE token_hash = :token_hash OR id_user = :id_user";

            mypdo::connect('delete');

            return mypdo::qry_exec($qry, $this->to_assoc_array(token_hash:true, id_user:true));
        }
    }

?>