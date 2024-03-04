<?php

    require_once __DIR__ . '/../../resource/storage/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/security/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/http/client.php';
    require_once __DIR__ . '/user_keys.php';
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