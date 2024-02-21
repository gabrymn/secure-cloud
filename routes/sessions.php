<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/sessions.php';
    require_once __DIR__ . '/../resource/http_response.php';

    function get_sessions_routes()
    {
        $router = new Router();

        $router->GET('/sessions', [], function() {

            AuthController::check_protectedarea();
            SessionController::render_sessions_page();
        });

        $router->POST('/sessions/expire', ['id_session'], function($args) {

            AuthController::check_protectedarea(false);
            SessionController::expire_session($args['id_session']);
        });

        $router->GET('/sessions/status', [], function() {

            AuthController::check_protectedarea(false);

            // At this point, the session status has already been verified in the previous
            // method [check_protected_area()], and the session status is valid. 
            // => Respond with HTTP status code 200.

            http_response::successful(200);

        });

        return $router->getRoutes();
    }

?>