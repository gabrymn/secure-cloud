<?php

    require_once __DIR__ . '/../resource/router.php';
    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/profile.php';
    
    function get_profile_routes()
    {
        $router = new Router();
        
        $router->GET('/profile', [], function() {

            AuthController::check_protectedarea();
            ProfileController::render_profile_page();
        });

        return $router->getRoutes();
    }

?>