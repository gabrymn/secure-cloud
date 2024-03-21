<?php

    require_once __DIR__ . '/../../utils/mypdo.php';
    require_once __DIR__ . '/../../utils/securekit/crypto_rnd_string.php';
    require_once __DIR__ . '/../../utils/httpkit/client.php';
    require_once __DIR__ . '/user_keys.php';
    require_once __DIR__ . '/../model/session.php';

    class TestController
    {
        public static function processTest()
        {
            include __DIR__ . '/../view/test.php';
        }
    }

?>