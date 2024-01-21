<?php

    class MYPDO extends PDO 
    {
        private function __construct($dsn, $username = null, $password = null, array $options = null) 
        {
            parent::__construct($dsn, $username, $password, $options);
        }
    
        public static function get_new_connection($user, $pwd, $host="mariadb_container", $dbname="secure_cloud", array $options = null) 
        {
            $mypdo = null;
            
            try 
            {
                $mypdo = new MYPDO("mysql:host=$host;dbname=$dbname", $user, $pwd, $options);
                $mypdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) 
            {
                return $e->getMessage();
            }
            return $mypdo;
        }
    
        public static function close_connection(&$mypdo_obj) 
        {
            $mypdo_obj = null;
            return true;
        }

        public static function prep(&$mypdo, $qry)
        {
            if ($mypdo)
            {
                $stmt = $mypdo->prepare($qry);
                return $stmt;
            }
            else return false;
        }

        public static function bindAllParams(array $params, &$stmt) 
        {
            $i = 1;

            foreach ($params as &$param) 
            {
                MYPDO::bindParam($param, $stmt, $i);
                $i++;
            }
        }

        public static function bindParam(&$param, &$stmt, $i = 1)
        {
            $paramType = is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindParam($i, $param, $paramType);
        }
    }

    /*
    elimina righe tabella '`secure-cloud.remember`_me' e '`secure-cloud.account_recovery`' scadute
    public static function del_expired_rows(){
        $qry = "DELETE FROM `secure-cloud`.`remember` WHERE expires <= NOW()";
        self::qry_exec($qry, false);
        $qry = "DELETE FROM `secure-cloud`.`account_recovery` WHERE expires <= NOW()";
        self::qry_exec($qry, false);
    }*/

?>