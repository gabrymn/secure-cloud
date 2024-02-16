<?php

    class UserSecurity 
    {
        private string $pwd_hash;
        private string $rkey_hash;
        private string $rkey_encrypted;
        private string $ckey_encrypted;
        private string $secret_2fa_encrypted;
        private string $dkey_salt;
        private int $id_user;

        private const DEFAULT_ATTR_VAL = "DEFAULT_ATTR_VALUE";
        private const DEFAULT_ID_USER_VAL = -1;

        public function __construct($pwd_hash=null, $rkey_hash=null, $rkey_encrypted=null, $ckey_encrypted=null, $secret_2fa_encrypted=null, $dkey_salt=null, $id_user=null)
        {
            self::set_pwd_hash($pwd_hash ? $pwd_hash : self::DEFAULT_ATTR_VAL);
            self::set_rkey_hash($rkey_hash ? $rkey_hash : self::DEFAULT_ATTR_VAL);
            self::set_rkey_encrypted($rkey_encrypted ? $rkey_encrypted : self::DEFAULT_ATTR_VAL);
            self::set_ckey_encrypted($ckey_encrypted ? $ckey_encrypted : self::DEFAULT_ATTR_VAL);
            self::set_secret_2fa_encrypted($secret_2fa_encrypted ? $secret_2fa_encrypted: self::DEFAULT_ATTR_VAL);
            self::set_dkey_salt($dkey_salt ? $dkey_salt: self::DEFAULT_ATTR_VAL);
            self::set_id_user($id_user ? $id_user : self::DEFAULT_ID_USER_VAL);
        }

        public function set_pwd_hash($pwd_hash)
        {
            $this->pwd_hash = $pwd_hash;
        }

        public function get_pwd_hash()
        {
            return $this->pwd_hash;
        }

        public function set_rkey_hash($rkey_hash)
        {
            $this->rkey_hash = $rkey_hash;
        }

        public function get_rkey_hash()
        {
            return $this->rkey_hash;
        }

        public function set_rkey_encrypted($rkey_encrypted)
        {
            $this->rkey_encrypted = $rkey_encrypted;
        }

        public function get_rkey_encrypted()
        {
            return $this->rkey_encrypted;
        }

        public function set_ckey_encrypted($ckey_encrypted)
        {
            $this->ckey_encrypted = $ckey_encrypted;
        }

        public function get_ckey_encrypted()
        {
            return $this->ckey_encrypted;
        }

        public function set_secret_2fa_encrypted($secret_2fa_encrypted)
        {
            $this->secret_2fa_encrypted = $secret_2fa_encrypted;
        }

        public function get_secret_2fa_encrypted()
        {
            return $this->secret_2fa_encrypted;
        }

        public function set_dkey_salt($dkey_salt)
        {
            $this->dkey_salt = $dkey_salt;
        }

        public function get_dkey_salt()
        {
            return $this->dkey_salt;
        }

        public function set_id_user($id_user)
        {
            $this->id_user = $id_user;
        }

        public function get_id_user()
        {
            return $this->id_user;
        }

        public function to_assoc_array($pwd_hash=false, $rkey_hash=false, $rkey_encrypted=false, $ckey_encrypted=false, $secret_2fa_encrypted=false, $dkey_salt=false, $id_user=false) : array
        {
            $params = array();
            
            if ($pwd_hash)
                $params["pwd_hash"] = $this->get_pwd_hash();

            if ($rkey_hash)
                $params["rkey_hash"] = $this->get_rkey_hash();

            if ($rkey_encrypted)
                $params["rkey_c"] = $this->get_rkey_encrypted();

            if ($ckey_encrypted)
                $params["ckey_c"] = $this->get_ckey_encrypted();
            
            if ($secret_2fa_encrypted)
                $params["secret_2fa_c"] = $this->get_secret_2fa_encrypted();

            if ($dkey_salt)
                $params["dkey_salt"] = $this->get_dkey_salt();

            if ($id_user)
                $params["id_user"] = $this->get_id_user();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO user_security (pwd_hash, rkey_hash, rkey_c, ckey_c, secret_2fa_c, dkey_salt, id_user)
            VALUES (:pwd_hash, :rkey_hash, :rkey_c, :ckey_c, :secret_2fa_c, :dkey_salt, :id_user)";

            mypdo::connect('insert');

            return mypdo::qry_exec
            (
                $qry, 
                $this->to_assoc_array
                (
                    pwd_hash:true,
                    rkey_hash:true,
                    rkey_encrypted:true,
                    ckey_encrypted:true,
                    secret_2fa_encrypted:true,
                    dkey_salt:true,
                    id_user:true
                )
            );
        }

        public function sel_pwd_hash_from_id()
        {
            $qry = "SELECT pwd_hash FROM user_security WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));
            
            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $pwd_hash = $res[0]['pwd_hash'];
                $this->set_pwd_hash($pwd_hash);
                return $this->get_pwd_hash();
            }
        }

        public function sel_secret_2fa_c_from_id()
        {
            $qry = "SELECT secret_2fa_c FROM user_security WHERE id_user = :id_user";
            
            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $secret_2fa_c = $res[0]['secret_2fa_c'];
                $this->set_secret_2fa_encrypted($secret_2fa_c);
                return $this->get_secret_2fa_encrypted();
            }
        }
        
        public function sel_rkey_from_id()
        {
            $qry = "SELECT rkey_c FROM user_security WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $rkey_c = $res[0]['rkey_c'];
                $this->set_rkey_encrypted($rkey_c);
                return $this->get_rkey_encrypted();
            }
        }

        public function sel_ckey_from_id()
        {
            $qry = "SELECT ckey_c FROM user_security WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $ckey_c = $res[0]['ckey_c'];
                $this->set_ckey_encrypted($ckey_c);
                return $this->get_ckey_encrypted();
            }
        }

        public function sel_dkey_salt_from_id()
        {
            $qry = "SELECT dkey_salt FROM user_security WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $this->to_assoc_array(id_user:true));

            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $dkey_salt = $res[0]['dkey_salt'];
                $this->set_dkey_salt($dkey_salt);
                return $this->get_dkey_salt();
            }
        }

        public function sel_rkey_hash_from_email(array $assoc_array)
        {
            $qry = 
            "SELECT rkey_hash 
            FROM user_security 
            WHERE id_user = (SELECT id FROM users WHERE email = :email)";
            
            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $assoc_array);

            if ($res === false)
                return false;

            if ($res === array())
                return null;
            else
            {
                $rkey_hash = $res[0]['rkey_hash'];
                $this->set_rkey_hash($rkey_hash);
                return $this->get_rkey_hash();
            }
        }


        public function upd_pwdhash_rkeyc_dkeysalt_from_iduser()
        {
            $qry = "UPDATE user_security 
            SET pwd_hash = :pwd_hash, rkey_c = :rkey_c, dkey_salt = :dkey_salt 
            WHERE id_user = :id_user";

            mypdo::connect('update');

            return mypdo::qry_exec
            (
                $qry, 
                $this->to_assoc_array
                (
                    pwd_hash:true, 
                    rkey_encrypted:true, 
                    dkey_salt:true, 
                    id_user:true
                )
            );
        }
    }

?>