<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/auth.php';
    require_once __DIR__ . '/../src/controller/sessions.php';
    require_once __DIR__ . '/../utils/httpkit/http_response.php';

    abstract class sessions implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/sessions', [], function() {
    
                AuthController::checkProtectedArea();
                SessionController::renderSessionsPage();
            });
    
            $router->POST('/sessions/expire', ['session_token'], function($args) {
                
                AuthController::checkProtectedarea(false);
                SessionController::expireSession($args['session_token']);
            });
    
            $router->GET('/sessions/current/status', [], function() {
    
                AuthController::checkProtectedarea(false);
    
                // At this point, the session status has already been verified in the previous
                // method [check_protected_area()], and the session status is valid. 
                // => Respond with HTTP status code 200.
    
                HttpResponse::successful(200);
    
            });
    
            return $router->getRoutes();
        }
    }

?>