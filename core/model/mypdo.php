<?php

    require_once 'device_info.php';

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

        private static function prep(&$mypdo, $qry)
        {
            if ($mypdo)
            {
                $stmt = $mypdo->prepare($qry);
                return $stmt;
            }
            else return false;
        }

        public static function sel_user_by_email(&$mypdo)
        {
            $d = include 'qry_paths.php';
            $qry_file = $d['SEL_USER_BY_EMAIL'];
            unset($d);

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = self::prep($mypdo, $qry);
                
                if (!$stmt)
                    return false;

                $qry_status = $stmt->execute();

                if (!$qry_status)
                    return false;
             
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $users;
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }
        
        public static function ins_user(&$mypdo, $user_data)
        {
            $d = include 'qry_paths.php';
            $qry_file = $d['INS_USER'];
            unset($d);

            if (!file_exists($qry_file))
                return false;
            $qry = file_get_contents($qry_file);

            try 
            {
                $stmt = self::prep($mypdo, $qry);
                if (!$stmt)
                    return false;

                self::bindAllParams($user_data, $stmt);
                return $stmt->execute();
            } 
            catch (PDOException $e)
            {   
                return $e->getMessage();
            }
        }

        private static function bindAllParams(array $params, &$stmt) 
        {
            foreach ($params as $key => &$value) 
            {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindParam(':' . $key, $value, $paramType);
            }
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



        /*
        
            ESEMPIO DI UTILIZZO:
            
            echo "<pre>";
            $conn = MYPDO::get_new_connection("USER_TYPE_INSERT", $_ENV['USER_TYPE_INSERT']);

            if (!$conn)
                http_response::server_error(500);

            $user_data = ["name"=>"franco", "surname"=>"buffon", "email"=>"gb@cwej.co", "pwd"=>"837gc", "2fa"=>0, "verified"=>0];
            
            if (!MYPDO::ins_user($conn, $user_data))
                http_response::server_error(500);

            MYPDO::close_connection($conn);

            $conn = MYPDO::get_new_connection("USER_TYPE_SELECT", $_ENV['USER_TYPE_SELECT']);

            if (!$conn)
                http_response::server_error(500);

            var_dump(MYPDO::sel_users($conn));
            MYPDO::close_connection($conn);

            echo "</pre>";
            exit;
        
        */ 

?>