<?php

    require_once __DIR__ . '/routes_interface.php';

    abstract class root implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/', [], function () {
        
                StaticPagesController::render_page('home');
            });

            return $router->getRoutes();
        }
    }

?>