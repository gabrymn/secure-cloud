<?php

    class User 
    {
        private $id;
        private string $email;
        private string $pwd;
        private string $name;
        private string $surname;
        private int $p2fa;
        private int $verified;

        public function __construct(){}

        public function init($email, $name, $surname, $p2fa=0, $verified=0){
            
            self::set_email($email);
            self::set_name($name);
            self::set_surname($surname);
            self::set_p2fa($p2fa);
            self::set_verified($verified);
        }

        public function set_id($id)
        {
            $this->id = $id;
        }

        public function get_id()
        {
            return $this->id;
        }

        public function set_email($email)
        {
            $this->email = htmlspecialchars($email);
        }

        public function set_pwd($pwd)
        {
            $this->pwd = htmlspecialchars($pwd);
        }

        public function get_pwd()
        {
            return $this->pwd;
        }

        public function set_name($name)
        {
            $this->name = htmlspecialchars($name);
        }

        public function set_surname($surname)
        {
            $this->surname = htmlspecialchars($surname);
        }

        public function set_p2fa($p2fa)
        {
            $this->p2fa = $p2fa;
        }

        public function set_verified($verified)
        {
            $this->verified = $verified;
        }

        public function get_email()
        {
            return $this->email;
        }

        public function get_name()
        {
            return $this->name;
        }

        public function get_surname()
        {
            return $this->surname;
        }

        public function get_p2fa()
        {
            return $this->p2fa;
        }

        public function get_verified()
        {
            return $this->verified;
        }

        public function get_all()
        {
            return
            [
                self::get_name(), 
                self::get_surname(),
                self::get_email(),
                self::get_p2fa(),
                self::get_verified()
            ];
        }



        public static function ins_user(&$mypdo, User $user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "ins_user.sql";

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindAllParams($user->get_all(), $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        // Model user
        public static function email_available(&$mypdo, $email, $qrys_dir)
        {
            if (QRY::sel_id_from_email($mypdo, $email, $qrys_dir) === -1)
                return 1;
            else
                return -1;
        }

        // Model user
        public static function sel_id_from_email(&$mypdo, $email, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_id_from_email.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($email, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $id_user = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // no users with that email
                if ($id_user === array())
                    return -1;
                else
                    return $id_user[0]['id'];
                    // array(USER_ID) === [0] => USER_ID
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        // Model user
        public static function del_user_from_email(&$conn, $email, $qrys_dir)
        {
            $qry_file = $qrys_dir . "del_user_from_email.sql";

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($email, $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }


        public static function sel_2fa_from_id(&$mypdo, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_2fa_from_id.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $p2fa = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($p2fa === array())
                    return false;
                else
                {
                    if (intval($p2fa[0]['2fa']))
                        return 1; // verified
                    else
                        return -1; // not verified
                }
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }


        // Model User
        public static function sel_email_from_id(&$mypdo, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_email_from_id.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $email = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // no users with that id
                if ($email === array())
                    return false;
                else
                    return $email[0]['email'];
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function upd_user_verified(&$conn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "upd_user_verified.sql";
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function sel_verified_from_id(&$conn, $id_user, $qrys_dir)
        {
            $qry_file = $qrys_dir . "sel_verified_from_id.sql";
            
            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = MYPDO::prep($conn, $qry);
                
                if (!$stmt)
                    return false;

                MYPDO::bindParam($id_user, $stmt);

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $verified = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($verified === array())
                    return $verified;
                else
                {
                    if (intval($verified[0]['verified']))
                        return 1;
                    else
                        return -1;
                }
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        public static function test()
        {
            return MYPDO::get_new_connection("root", "root");
        }
    }

?>