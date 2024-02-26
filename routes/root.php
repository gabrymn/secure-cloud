<?php

    require_once __DIR__ . '/routesInterface.php';
    require_once __DIR__ . '/../src/controller/staticPages.php';

    abstract class root implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/', [], function () {
        
                StaticPagesController::renderPage('home');
            });

            return $router->getRoutes();
        }
    }

?>