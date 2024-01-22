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
        
        public function init($email, $pwd, $name, $surname, $p2fa=0, $verified=0){
            
            self::set_email(htmlspecialchars($email));
            self::set_pwd($pwd);
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

        public function get_pwd()
        {
            return $this->pwd;
        }

        public function get_pwd_hashed($algo = PASSWORD_BCRYPT)
        {
            return password_hash($this->pwd, $algo);
        }

        public function get_all()
        {
            return
            [
                self::get_name(), 
                self::get_surname(),
                self::get_email(),
                self::get_pwd_hashed(),
                self::get_p2fa(),
                self::get_verified()
            ];
        }


        
    }

?>