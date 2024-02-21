<?php

    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../model/model.php';

    class GeneralModel extends Model
    {
        public function to_assoc_array()
        {
            return "ewohicpweokc";
        }

        public function ins()
        {
            return true;
        }
    }

    class TestController
    {
        public static function process_test()
        {
            $host = "mariadb_container";
            $dbname = "secure_cloud";

            try
            {
                new PDO
                (
                    "mysql:host=$host;charset=utf8mb4",
                    "root",
                    "root"
                );
            }
            catch (PDOException $e)
            {
                echo $e->getMessage();
            }
        }
    }
?>