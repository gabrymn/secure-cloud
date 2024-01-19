<?php

    require_once __ROOT__ . 'model/http/http_response.php';

    function handle_get()
    {
        if (count($_GET) !== 0)
            http_response::client_error(404);
    }

?>