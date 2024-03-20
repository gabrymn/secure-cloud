<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/test.php';

    abstract class test implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();
                    
            $router->GET('/test', [], function () {
        
                TestController::processTest();
            });

            return $router->getRoutes();
        }
    }

?>