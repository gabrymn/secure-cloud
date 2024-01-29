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

    function htmlspecialchars_array(array &$array)
    {
        foreach ($array as $key => $val)
            $array[$key] = htmlspecialchars($val);
    }

?>