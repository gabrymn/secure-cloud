<?php

    require_once __DIR__ . '/../../resource/storage/fileSysHandler.php';
    require_once __DIR__ . '/userKeys.php';

    class TestController
    {
        public static function processTest()
        {
            session_start();
            
            echo UserKeysController::getRecoveryKey();
            echo "<br>";
            echo UserKeysController::getCipherKey();
        }
    }

?>