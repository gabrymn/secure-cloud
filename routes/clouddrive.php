<?php

    require_once __DIR__ . '/routes_interface.php';
    require_once __DIR__ . '/../src/controller/auth.php';
    require_once __DIR__ . '/../src/controller/clouddrive.php';
    require_once __DIR__ . '/../src/controller/file_uploader.php';
    require_once __DIR__ . '/../src/controller/file_downloader.php';

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
                FileUploaderController::initializeUploadSession($args['upload_space_required']);
            }); 
            
            $router->POST('/clouddrive/upload', ['file', 'upload_session_id', 'filename', 'mimetype', 'chunk_index', 'chunks_len'], function($args) {
                
                AuthController::checkProtectedArea(redirect: false);
                FileUploaderController::handleUploadStreaming($args);
            }); 
    
            $router->GET('/clouddrive/download', ['fileid'], function($args) {

                AuthController::checkProtectedArea(redirect: false);
                FileDownloaderController::processDownload($args['fileid']);
            }); 
    
            return $router->getRoutes();
        }
    }

?>