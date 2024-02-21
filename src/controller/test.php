<?php

    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';

    class TestController
    {
        public static function process_test()
        {
            $rstr = new CryptoRNDString();

            echo $rstr->generate(32);
            echo $rstr->generate(32);
            echo $rstr->generate(32);
            echo $rstr->generate(32);
            echo $rstr->generate(32);
            echo $rstr->generate(32);
            echo $rstr->generate(32);

        }
    }
?>