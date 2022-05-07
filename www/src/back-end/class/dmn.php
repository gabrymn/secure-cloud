<?php

    function get_dmn($option = "lh")
    {
        define('DMNS', [
            "lh" => "http://127.0.0.1",
            "mywebs" => "https://mywebs.altervista.org"
        ]);

        return DMNS[$option];
    }
?>