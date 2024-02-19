<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/transfers.php';

    function get_transfers_routes()
    {
        $router = new Router();

        $router->GET('/transfers', [], function() {

            AuthController::check_protectedarea();
            TransfersController::render_transfers_page();
        }); 

        return $router->getRoutes();
    }

?>