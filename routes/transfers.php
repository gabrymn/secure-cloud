<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../core/controller/auth.php';
    require_once __DIR__ . '/../core/controller/transfers.php';

    abstract class transfers implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/transfers', [], function() {
    
                AuthController::checkProtectedArea();
                TransfersController::renderTransfersPage();
            }); 
    
            return $router->getRoutes();
        }
    }

?>