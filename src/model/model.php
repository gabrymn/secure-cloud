<?php

    require_once __DIR__ . '/../../resource/security/crypto_rnd_string.php';

    abstract class Model
    {
        protected const DEFAULT_STR = "DEFAULT_STR_VALUE";
        protected const DEFAULT_INT = -1;

        protected const TZ = 'Europe/Rome';
        protected const DATE_FORMAT = 'Y-m-d H:i:s';

        abstract public function toAssocArray();

        abstract public function ins();

        protected function generateUID(int $len, ?string $alphabet = null) : string
        {
            $crypto_rnd = new CryptoRNDString($alphabet);
            return $crypto_rnd->generate($len);
        }
    }

?>