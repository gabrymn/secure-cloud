<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/auth.php';
    require_once __DIR__ . '/../src/controller/profile.php';
    
    abstract class profile implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();
            
            $router->GET('/profile', [], function() {
                
                AuthController::checkProtectedArea();
                ProfileController::renderProfilePage();
            });
            
            return $router->getRoutes();
        }
    }

?>