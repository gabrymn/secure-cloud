<?php

    abstract class MyPDO
    {
        private static ?PDO $conn = null;
        private static ?PDOStatement $stmt = null;

        private static ?string $inputs_hash = null; 

        public static function connect($username, $password, $host, $dbname, array $options = null)
        {
            if (self::isConnected($username, $password, $host, $dbname))
                return true;

            self::$inputs_hash = hash("sha256", $username.$password.$host.$dbname);

            try 
            {
                self::$conn = new PDO
                (
                    "mysql:host=$host;dbname=$dbname", 
                    $username, 
                    $password, 
                    $options
                );

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) 
            {
                return $e->getMessage();
            }

            return true;
        }

        public static function isConnected($username, $password, $host, $dbname) : bool
        {
            if (self::$conn === null)
                return false;

            $inputs_hash_user = hash("sha256", $username.$password.$host.$dbname);

            return hash_equals(self::$inputs_hash, $inputs_hash_user);
        }

        
        public static function qryexec(string $qry, array $params = null, bool $data_expected = false)
        {
            self::prep($qry);

            if ($params !== null)
                self::bindAllParams($params);

            $qry_status = self::$stmt->execute();

            $response = null;

            if ($data_expected === true)
                $response =  self::$stmt->fetchAll(PDO::FETCH_ASSOC);
            else
                $response = $qry_status;

            return $response;
        }

        private static function prep(string $qry)
        {
            if (self::$conn !== null)
            {
                self::$stmt = self::$conn->prepare($qry);
                return true;
            }
            else
                return false;
        }
   
        private static function bindAllParams(array &$params) 
        {
            foreach (array_keys($params) as $key) 
            {
                self::$stmt->bindParam
                (
                    ':' . $key, 
                    $params[$key], 
                    is_int($params[$key]) ? PDO::PARAM_INT : PDO::PARAM_STR
                );
            }
        }

        public static function beginTransaction()
        {
            if (self::$conn !== null)
                return self::$conn->beginTransaction();
            else
                return false;
        }

        public static function commit()
        {
            if (self::$conn === null)
                return false;
            else
                return self::$conn->commit();
        }

        public static function rollback()
        {
            if (self::$conn === null)
                return false;
            else
                return self::$conn->rollBack();
        }
    }

?>