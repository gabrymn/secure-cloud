<?php

    class MyPDO
    {
        private static string|null $operation_type = null;
        private static PDO|null $conn = null;
        private static PDOStatement|null $stmt = null;

        public const SELECT = 'select';
        public const EDIT = 'edit';
        private const OPERATION_TYPES = [self::SELECT, self::EDIT];

        public static function connect($operation_type, $host=null, $dbname=null, array $options = null)
        {
            if (!in_array($operation_type, self::OPERATION_TYPES))
                return false;

            if ($host === null)
                $host = $_ENV['DATABASE_HOST'];

            if ($dbname === null)
                $dbname = $_ENV['DATABASE_NAME'];

            self::setOperationType($operation_type);
            
            // already connected
            if (self::$conn !== null && self::$operation_type === $operation_type)
                return true;

            $credentials = self::getCredentials($operation_type);

            try 
            {
                self::$conn = new PDO
                (
                    "mysql:host=$host;dbname=$dbname", 
                    $credentials['username'], 
                    $credentials['password'], 
                    $options
                );

                self::setOperationType($operation_type);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) 
            {
                return $e->getMessage();
            }

            return true;
        }

        private static function setOperationType($operation_type) : void
        {
            self::$operation_type = $operation_type;
        }

        private static function getOperationType() : string|null
        {
            return self::$operation_type;
        }

        public static function qryExec(string $qry, array $params = null)
        {
            self::prep($qry);

            if ($params !== null)
                self::bindAllParams($params);

            $qry_status = self::$stmt->execute();

            $response = null;

            if (self::getOperationType() !== self::SELECT)
                $response = $qry_status;
            else
                $response =  self::$stmt->fetchAll(PDO::FETCH_ASSOC);

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

        private static function getCredentials(string $operation_type) : array|false
        {
            $credentials =
            [
                'username' => null,
                'password' => null
            ];

            switch (strtolower($operation_type))
            {
                case self::SELECT:
                {
                    $credentials['username'] = $_ENV['USER_SELECT_USERNAME'];
                    $credentials['password'] = $_ENV['USER_SELECT_PASSWORD'];
                    break;
                };

                case self::EDIT:
                {
                    $credentials['username'] = $_ENV['USER_EDIT_USERNAME'];
                    $credentials['password'] = $_ENV['USER_EDIT_PASSWORD'];
                    break;
                };

                default:
                {
                    $credentials = false;
                    break;
                }
            }

            return $credentials;
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

        public static function rollBack()
        {
            if (self::$conn === null)
                return false;
            else
                return self::$conn->rollBack();
        }
    }

?>