<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/storage.php';

    function get_storage_routes()
    {
        $router = new Router();

        $router->GET('/storage', [], function() {

            AuthController::check_protectedarea();
            StorageController::render_storage_page();
        }); 

        return $router->getRoutes();
    }

?>