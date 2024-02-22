<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/clouddrive.php';

    function get_clouddrive_routes()
    {
        $router = new Router();

        $router->GET('/clouddrive', [], function() {

            AuthController::check_protectedarea();
            CloudDriveController::render_clouddrive_page();
        }); 
        
        $router->POST('/clouddrive/upload', ['file', 'filename', 'index', 'chunks_n'], function($file) {
            
            AuthController::check_protectedarea(redirect: false);
            CloudDriveController::upload($file);
        }); 

        return $router->getRoutes();
    }

?>