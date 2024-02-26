<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/auth_checker.php';
    require_once __DIR__ . '/../src/controller/cloud/clouddrive.php';
    require_once __DIR__ . '/../src/controller/cloud/file_uploader.php';

    abstract class clouddrive implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/clouddrive', [], function() {
    
                AuthController::check_protectedarea();
                CloudDriveController::render_clouddrive_page();
            }); 
    
            $router->POST('/clouddrive/upload/initialize', ['upload_space_required'], function($args) {
                
                AuthController::check_protectedarea(redirect: false);
                FileUploaderController::initializeUploadSession($args);
            }); 
            
            $router->POST('/clouddrive/upload', ['file', 'upload_session_id', 'filename', 'filetype', 'chunk_index', 'chunks_len'], function($args) {
                    
                AuthController::check_protectedarea(redirect: false);
                FileUploaderController::handleUploadStreaming($args);
            }); 
    
            $router->GET('/clouddrive/download', ['id_file'], function($args) {
                
                AuthController::check_protectedarea(redirect: false);
                CloudDriveController::handle_download_of($args['id_file']);
            }); 
    
            return $router->getRoutes();
        }
    }

?>