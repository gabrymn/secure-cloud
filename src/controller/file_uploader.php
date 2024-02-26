<?php

    require_once __DIR__ . '/../../../resource/http/http_response.php';
    require_once __DIR__ . '/../../../resource/http/upload_session.php';
    require_once __DIR__ . '/../../model/user.php';

    class FileUploaderController
    {
        public static function initializeUploadSession($args)
        {
            $user = new UserModel(id_user: $_SESSION['ID_USER']);

            if ($user->enoughStorage($args['upload_space_required']) === 0)
            {
                httpResponse::clientError(400, "Storage space available exceeded, delete files first");
            }
            
            UploadSession::initialize($args['upload_space_required']);

            httpResponse::successful
            (
                200, 
                'Upload session initialized successfully', 
                [
                    "upload_session_id" => UploadSession::getID(),
                    "chunk_size" => UploadSession::UPLOAD_CHUNK_SIZE 
                ]
            );
        }

        public static function handleUploadStreaming($args)
        {   
            $id_is_valid = UploadSession::checkID($args['upload_session_id']);
            
            if (!$id_is_valid)
                httpResponse::clientError(400, "Incorrect Upload-session ID");
            else
                UploadSession::setID($args['upload_session_id']);


            $space_ok = UploadSession::checkUsedSpace(chunk_size: $args['file']['size']);
            
            if (!$space_ok)
                httpResponse::clientError(400, "Upload space limit exeeded");

            UploadSession::storeChunk
            (
                $args['filename'], 
                $args['file']['tmp_name'], 
                $args['chunk_index']
            );

            UploadSession::increaseUsedSpace($args['file']['size']);

            $response_msg = "Chunk uploaded";

            // This if is true when a single file is 100% uploaded.
            if (UploadSession::fileUploadIsCompleted($args['filename'], $args['chunks_len']))
            {
                UploadSession::concatChunks($args['filename']);
                
                $response_msg = "File " . $args['filename'] . "uploaded successful";

                // Since a user can upload multiple files using the same session
                // we have to check wheter all files have been uploaded
                $all_files_uploaded = UploadSession::uploadIsCompleted();
                
                if ($all_files_uploaded)
                {
                    UploadSession::destroy();
                    $response_msg = "All files have been uploaded";
                }
            }

            httpResponse::successful(200, $response_msg);
        }
    }

?>
