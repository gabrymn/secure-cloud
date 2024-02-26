<?php

    require_once __DIR__ . '/routesInterface.php';
    require_once __DIR__ . '/../src/controller/auth.php';
    require_once __DIR__ . '/../src/controller/clouddrive.php';
    require_once __DIR__ . '/../src/controller/fileUploader.php';

    abstract class clouddrive implements RoutesInterface
    {
        public static function getRoutes()
        {
            $router = new Router();

            $router->GET('/clouddrive', [], function() {
    
                AuthController::checkProtectedArea();
                CloudDriveController::renderClouddrivePage();
            }); 
    
            $router->POST('/clouddrive/upload/initialize', ['upload_space_required'], function($args) {
                
                AuthController::checkProtectedArea(redirect: false);
                FileUploaderController::initializeUploadSession($args);
            }); 
            
            $router->POST('/clouddrive/upload', ['file', 'upload_session_id', 'filename', 'filetype', 'chunk_index', 'chunks_len'], function($args) {
                    
                AuthController::checkProtectedArea(redirect: false);
                FileUploaderController::handleUploadStreaming($args);
            }); 
    
            $router->GET('/clouddrive/download', ['id_file'], function($args) {
                
                AuthController::checkProtectedArea(redirect: false);
                CloudDriveController::handleDownloadOf($args['id_file']);
            }); 
    
            return $router->getRoutes();
        }
    }

?>