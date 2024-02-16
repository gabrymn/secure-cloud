<?php

    class EmailVerify 
    {
        private string $tkn_hash;
        private int $id_user;
        private $expires;
        private string $email;

        private const DEFAULT_EMAIL_VAL = "EMAIL_DEFAULT_VALUE";
        private const DEFAULT_ID_USER_VAL = -1;
        private const DEFAULT_EXPIRES_VAL = "EXPIRES_DEFAULT_VALUE";
        private const DEFAULT_TOKEN_HASH_VAL = "TOKEN_HASH_DEFAULT_VALUE";

        private const EXP_MINUTES = 30;
        private const TZ = 'Europe/Rome';
        private const DATE_FORMAT = 'Y-m-d H:i:s';

        public function __construct($tkn_hash = null, $id_user = null, $expires = null, $email = null)
        {
            date_default_timezone_set(self::TZ);
            self::set_tkn_hash($tkn_hash ? $tkn_hash : self::DEFAULT_TOKEN_HASH_VAL);
            self::set_expires($expires ? $expires : $this->set_expires());
            self::set_id_user($id_user ? $id_user : self::DEFAULT_ID_USER_VAL);
            self::set_email($email ? $email : self::DEFAULT_EMAIL_VAL);
        }

        public function set_email($email)
        {
            $this->email = $email;
        }

        public function get_email()
        {
            return $this->email;
        }

        public function get_tkn_hash()
        {
            return $this->tkn_hash;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function get_expires()
        {
            return $this->expires;
        }

        public function set_tkn_hash($tkn_hash)
        {
            $this->tkn_hash = $tkn_hash;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function set_expires($expires = false)
        {
            if (!$expires)
                $expires = date(self::DATE_FORMAT, strtotime("+" . strval(self::EXP_MINUTES) . " minutes", time()));
            
            $this->expires = $expires;
        }

        public function check_expires() : bool
        {
            $expires = new DateTime(self::get_expires());
            $now = new DateTime(date(self::DATE_FORMAT));

            return $expires < $now;
        }

        public function to_assoc_array($tkn_hash = false, $expires = false, $email = false, $id_user = false)
        {
            $params = array();

            if ($tkn_hash)
                $params['tkn_hash'] =  $this->get_tkn_hash();

            if ($expires)
                $params['expires'] =  $this->get_expires();

            if ($email)
                $params['email'] =  $this->get_email();

            if ($id_user)
                $params['id_user'] =  $this->get_id_user();

            return $params;
        }

        /**
            query insert email_verify object 
        */
        public function ins($email_insert = false) : bool
        {
            $qry = "INSERT INTO email_verify (tkn_hash, expires, id_user) VALUES (:tkn_hash, :expires, :id_user)";

            mypdo::connect('insert');

            return mypdo::qry_exec
            (
                $qry, 
                $this->to_assoc_array(tkn_hash:true, expires:true, email:$email_insert, id_user:true)
            );
        }

        public function sel_id_from_tkn()
        {
            $qry = "SELECT id_user FROM email_verify WHERE tkn_hash = :tkn_hash";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(tkn_hash:true));

            if ($res === false)
                return false;

            if ($res === array())
                return -1;
            else
            {
                $id_user = intval($res[0]['id_user']);
                $this->set_id_user($id_user);
                return $this->get_id_user();
            }
        }

        public function del_ver_from_tkn()
        {
            $qry = "DELETE FROM email_verify WHERE tkn_hash = :tkn_hash OR id_user = :id_user";

            mypdo::connect('delete');

            return mypdo::qry_exec($qry, $this->to_assoc_array(tkn_hash:true, id_user:true));
        }
    }

?>