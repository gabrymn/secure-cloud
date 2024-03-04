<?php

    require_once __DIR__ . '/../../resource/storage/fileSysHandler.php';
    require_once __DIR__ . '/../../resource/security/cryptoRNDString.php';
    require_once __DIR__ . '/../../resource/http/client.php';
    require_once __DIR__ . '/userKeys.php';
    require_once __DIR__ . '/../model/session.php';

    class TestController
    {
        public static function processTest()
        {
            $session = new SessionModel
            (
                ip: client::getIP(), 
                os:client::getOS(), 
                browser: client::getBrowser(),
                expired: 2
            );

            $session->ins();

        }
    }

?>