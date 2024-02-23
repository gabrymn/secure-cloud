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

        $router->POST('/clouddrive/upload/initialize', ['upload_space_required'], function($args) {
            
            AuthController::check_protectedarea(redirect: false);
            FileUploaderController::initialize_upload_session($args);
        }); 
        
        $router->POST('/clouddrive/upload', ['file', 'upload_session_id', 'filename', 'filetype', 'index', 'chunks_len'], function($args) {

            AuthController::check_protectedarea(redirect: false);
            FileUploaderController::handle_filechunk($args);
        }); 

        return $router->getRoutes();
    }

?>