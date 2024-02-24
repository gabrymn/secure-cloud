<?php

    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/cloud/clouddrive.php';
    require_once __DIR__ . '/../src/controller/cloud/file_uploader.php';

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
        
        $router->POST('/clouddrive/upload', ['file', 'upload_session_id', 'filename', 'filetype', 'chunk_index', 'chunks_len'], function($args) {
                
            AuthController::check_protectedarea(redirect: false);
            FileUploaderController::handle_upload_streaming($args);
        }); 

        $router->GET('/clouddrive/download', ['id_file'], function($args) {
            
            AuthController::check_protectedarea(redirect: false);
            CloudDriveController::handle_download_of($args['id_file']);
        }); 

        return $router->getRoutes();
    }

?>