<?php

    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../model/model.php';
    
    class TestController
    {
        public static function process_test()
        {
            mypdo::connect('insert');

            print_r(mypdo::qry_exec("SELECT * FROM users"));
        }
    }
?>