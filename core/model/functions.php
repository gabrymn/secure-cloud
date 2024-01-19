<?php

    function key_contains(&$array, ...$args)
    {
        foreach ($args as $arg)
        {
            if (!isset($array[$arg]))
                return false;    
        }
        return true;
    }

?>