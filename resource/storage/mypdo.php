<?php

    class MyPDO
    {
        private static string|null $operation_type = self::OPERATION_TYPES[0];
        private static PDO|null $conn = null;
        private static PDOStatement|null $stmt = null;

        private const OPERATION_TYPES = ['select', 'insert', 'update', 'delete'];

        public static function connect($operation_type = self::OPERATION_TYPES[0], $user='root', $host="mysql_container", $dbname="secure_cloud", array $options = null)
        {
            if (!in_array($operation_type, self::OPERATION_TYPES))
                return false;

            self::setOperationType($operation_type);
            
            // already connected
            if (self::$conn !== null)
                return true;

            $credentials = self::getCredentials($user);

            try 
            {
                self::$conn = new PDO
                (
                    "mysql:host=$host;dbname=$dbname", 
                    $credentials['username'], 
                    $credentials['pwd'], 
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

            if (self::getOperationType() !== 'select')
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

        private static function getCredentials($user) : array|null
        {
            $credentials =
            [
                'username' => null,
                'pwd' => null
            ];

            switch (strtolower($user))
            {
                case 'admin':
                {
                    $credentials['username'] = 'USER_ADMIN';
                    $credentials['pwd'] = $_ENV['USER_ADMIN_PWD'];
                    break;
                };

                case 'root':
                {
                    $credentials['username'] = 'root';
                    $credentials['pwd'] = 'root';
                    break;
                };

                case 'select':
                {
                    $credentials['username'] = 'USER_SEL';
                    $credentials['pwd'] = $_ENV['USER_SEL_PWD'];
                    break;
                };

                case 'insert':
                {
                    $credentials['username'] = 'USER_INS';
                    $credentials['pwd'] = $_ENV['USER_INS_PWD'];
                    break;
                };

                case 'update':
                {
                    $credentials['username'] = 'USER_UPD';
                    $credentials['pwd'] = $_ENV['USER_UPD_PWD'];
                    break;
                };

                case 'delete':
                {
                    $credentials['username'] = 'USER_DEL';
                    $credentials['pwd'] = $_ENV['USER_DEL_PWD'];
                    break;
                };

                default:
                {
                    $credentials = null;
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