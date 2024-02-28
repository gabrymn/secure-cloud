<?php   

    require_once __DIR__ . '/model.php';

    class UserModel extends Model
    {
        private int $id_user;
        private string $email;
        private string $name;
        private string $surname;
        private int $p2fa;
        private int $verified;

        private const DEFAULT_2FA = 0;
        private const DEFAULT_VERIFIED = 0;

        public function __construct($id_user = null, $email = null, $name = null, $surname = null, $p2fa = null, $verified = null)
        {
            self::setUserID($id_user ? $id_user : parent::DEFAULT_INT);
            self::setEmail($email ? $email : parent::DEFAULT_STR);
            self::setName($name ? $name : parent::DEFAULT_STR);
            self::setSurname($surname ? $surname : parent::DEFAULT_STR);
            self::set2FA($p2fa ? $p2fa : self::DEFAULT_2FA);
            self::setVerified($verified ? $verified : self::DEFAULT_VERIFIED);
        }

        private function formatName($name)
        {
            // "John"
            // "Doe"
            return ucfirst(strtolower($name));
        }

        public function setUserID($id_user)
        {
            $this->id_user = $id_user;
        }

        public function getUserID()
        {
            return $this->id_user;
        }

        public function setEmail($email)
        {
            $this->email = strtolower($email);
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setName($name)
        {
            $this->name = self::formatName($name);
        }

        public function getName()
        {
            return $this->name;
        }

        public function setSurname($surname)
        {
            $this->surname = self::formatName($surname);
        }

        public function getSurname()
        {
            return $this->surname;
        }

        public function set2FA($p2fa)
        {
            $this->p2fa = $p2fa;
        }

        public function get2FA()
        {
            return $this->p2fa;
        }

        public function setVerified($verified)
        {
            $this->verified = $verified;
        }

        public function getVerified()
        {
            return $this->verified;
        }

        public function toAssocArray(bool $id_user=false, bool $email=false, bool $name=false, bool $surname=false, bool $p2fa=false, bool $verified=false)
        {
            $params = array();
            
            if ($id_user)
                $params["id_user"] = $this->getUserID();

            if ($email)
                $params["email"] = $this->getEmail();

            if ($name)
                $params["name"] = $this->getName();

            if ($surname)
                $params["surname"] = $this->getSurname();
            
            if ($p2fa)
                $params["p2fa"] = $this->get2FA();

            if ($verified)
                $params["verified"] = $this->getVerified();

            return $params;
        }

        /**
         * insertion user SQL query
         */
        public function ins()
        {
            $qry = "INSERT INTO users (email,name,surname) VALUES (:email,:name,:surname)";

            MyPDO::connect('insert');
            return MyPDO::qryExec($qry, $this->toAssocArray(email:true,name:true,surname:true));
        }

        public function sel_userID_by_email()
        {
            $qry = "SELECT id_user FROM users WHERE email = :email";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(email:true));

            if ($res === false)
                return false;
            
            if ($res === array())
                return -1;
            else
            {
                $id_user = $res[0]['id_user'];
                $this->setUserID($id_user);
                return $this->getUSerID();
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
            $id_user = $this->sel_userID_by_email();
            
            if ($id_user === false)
                return false;
            else if ($id_user === -1)
                return 0;
            else
                return 1;
        }

        public function sel_2FA_by_userID() : int|bool
        {
            $qry = "SELECT 2fa FROM users WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === array())
                return null;
            else
            {
                $p2fa = intval($res[0]['2fa']);
                $this->set2FA($p2fa);
                return $this->get2FA();
            }
        }

        public function sel_verified_by_userID() : int|bool
        {
            $qry = "SELECT verified FROM users WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === array())
                return null;
            else
            {
                $verified = intval($res[0]['verified']);
                $this->setVerified($verified);
                return $this->getVerified();
            }
        }

        public function sel_email_by_userID()
        {
            $qry = "SELECT email FROM users WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));

            if ($res === array())
                return false;
            else
            {
                $email = $res[0]['email'];
                $this->setEmail($email);
                return $this->getEmail();
            }
        }

        public function upd_user_to_verified()
        {
            $qry = "UPDATE users SET verified = 1 WHERE id_user = :id_user";
            MyPDO::connect('update');
            return MyPDO::qryExec($qry, $this->toAssocArray(id_user:true));
        }


        /**
         * Check if the provided storage space, in bytes, does not exceed the available storage space
         * allocated to the user in their cloud storage plan.
         *
         * @param int $storage The amount of storage space, in bytes, to check.
         *
         * @return int|false Returns an integer value (1) if the storage space is within the user's limit,
         *                  indicating success. Returns (0) if the provided storage space exceeds the
         *                  user's allocated storage limit.
         *                  Returns (false) if there is an error/exception 
         */
        public function enoughStorage(int $storage) : int|false
        {
            return 1;
        }
    }

?>