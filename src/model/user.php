<?php   

    require_once __DIR__ . '/model.php';

    class User extends Model
    {
        private int $id_user;
        private string $email;
        private string $name;
        private string $surname;
        private int $p2fa;
        private int $verified;

        private const DEFAULT_ID_VAL = -1;
        private const DEFAULT_EMAIL_VAL = "DEFAULT_EMAIL_VALUE";
        private const DEFAULT_NAME_VAL = "DEFAULT_NAME_VALUE";
        private const DEFAULT_SURNAME_VAL = "DEFAULT_SURNAME_VALUE";
        private const DEFAULT_2FA_VAL = 0;
        private const DEFAULT_VERIFIED_VAL = 0;

        public function __construct($id_user = null, $email = null, $name = null, $surname = null, $p2fa = null, $verified = null)
        {
            self::set_id_user($id_user ? $id_user : self::DEFAULT_ID_VAL);
            self::set_email($email ? $email : self::DEFAULT_EMAIL_VAL);
            self::set_name($name ? $name : self::DEFAULT_NAME_VAL);
            self::set_surname($surname ? $surname : self::DEFAULT_SURNAME_VAL);
            self::set_p2fa($p2fa ? $p2fa : self::DEFAULT_2FA_VAL);
            self::set_verified($verified ? $verified : self::DEFAULT_VERIFIED_VAL);
        }

        private function format_name($name)
        {
            // "John"
            // "Doe"
            return ucfirst(strtolower($name));
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function set_email($email)
        {
            $this->email = strtolower($email);
        }

        public function get_email()
        {
            return $this->email;
        }

        public function set_name($name)
        {
            $this->name = self::format_name($name);
        }

        public function get_name()
        {
            return $this->name;
        }

        public function set_surname($surname)
        {
            $this->surname = self::format_name($surname);
        }

        public function get_surname()
        {
            return $this->surname;
        }

        public function set_p2fa($p2fa)
        {
            $this->p2fa = $p2fa;
        }

        public function get_p2fa()
        {
            return $this->p2fa;
        }

        public function set_verified($verified)
        {
            $this->verified = $verified;
        }

        public function get_verified()
        {
            return $this->verified;
        }

        public function to_assoc_array(bool $id_user=false, bool $email=false, bool $name=false, bool $surname=false, bool $p2fa=false, bool $verified=false)
        {
            $params = array();
            
            if ($id_user)
                $params["id_user"] = $this->get_id_user();

            if ($email)
                $params["email"] = $this->get_email();

            if ($name)
                $params["name"] = $this->get_name();

            if ($surname)
                $params["surname"] = $this->get_surname();
            
            if ($p2fa)
                $params["p2fa"] = $this->get_p2fa();

            if ($verified)
                $params["verified"] = $this->get_verified();

            return $params;
        }

        /**
         * insertion user SQL query
         */
        public function ins()
        {
            $qry = "INSERT INTO users (email,name,surname) VALUES (:email,:name,:surname)";

            mypdo::connect('insert');
            return mypdo::qry_exec($qry, $this->to_assoc_array(email:true,name:true,surname:true));
        }

        public function sel_id_from_email()
        {
            $qry = "SELECT id_user FROM users WHERE email = :email";

            echo mypdo::connect('select')?"SI":"NO";
            exit;

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(email:true));

            if ($res === false)
                return false;
            
            if ($res === array())
                return -1;
            else
            {
                $id_user = $res[0]['id_user'];
                $this->set_id_user($id_user);
                return $this->get_id_user();
            }
        }


        /**
         * Check if the email is already taken in the database.
         *
         * @return bool|int Returns 
         *   false if there is an internal query error,
         *   0 if the email is available, 
         *   1 if the email is already taken.
         */
        public function email_is_taken() : int|bool
        {
            switch ($this->sel_id_from_email())
            {
                case false:
                    return false;
                case -1:
                    return 0;
                default:
                    return 1;
            }
        }

        public function sel_2fa_from_id() : int|bool
        {
            $qry = "SELECT 2fa FROM users WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === array())
                return null;
            else
            {
                $p2fa = intval($res[0]['2fa']);
                $this->set_p2fa($p2fa);
                return $this->get_p2fa();
            }
        }

        public function sel_verified_from_id() : int|bool
        {
            $qry = "SELECT verified FROM users WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === array())
                return null;
            else
            {
                $verified = intval($res[0]['verified']);
                $this->set_verified($verified);
                return $this->get_verified();
            }
        }

        public function sel_email_from_id()
        {
            $qry = "SELECT email FROM users WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === array())
                return false;
            else
            {
                $email = $res[0]['email'];
                $this->set_email($email);
                return $this->get_email();
            }
        }

        public function upd_user_to_verified()
        {
            $qry = "UPDATE users SET verified = 1 WHERE id_user = :id_user";
            mypdo::connect('update');
            return mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));
        }
    }

?>