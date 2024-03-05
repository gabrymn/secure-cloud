<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../core/controller/auth.php';
    require_once __DIR__ . '/../core/controller/storage.php';

    abstract class storage implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/storage', [], function() {
    
                AuthController::checkProtectedArea();
                StorageController::renderStoragePage();
            }); 
    
            return $router->getRoutes();
        }
    }

?>