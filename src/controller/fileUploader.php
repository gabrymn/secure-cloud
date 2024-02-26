<?php

    require_once __DIR__ . '/../../resource/http/httpResponse.php';
    require_once __DIR__ . '/../../resource/http/uploadSession.php';
    require_once __DIR__ . '/../../resource/storage/myPDO.php';
    require_once __DIR__ . '/../../resource/storage/fileSysHandler.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/fileTransfer.php';
    require_once __DIR__ . '/userKeys.php';

    class FileUploaderController
    {
        private const ROOT_STORAGE_DIR = __DIR__ . '/../../storage';

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

            self::storeChunk
            (
                $args['filename'], 
                $args['file']['tmp_name'], 
                $args['chunk_index'],
                $args['file']['size']
            );

            $response_msg = "Chunk uploaded";

            // This if is true when a single file is 100% uploaded.
            if (UploadSession::fileUploadIsCompleted($args['filename'], $args['chunks_len']))
            {
                self::concatChunks($args['filename']);
                self::processFileEncryption($args['filename'], $filename_encrypted);
                self::storeFileMetaData($args['filename'], $filename_encrypted, $args['mimetype']);

                $response_msg = "File " . $args['filename'] . " uploaded successfully";

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

        private static function storeFileMetaData($filename, $filename_encrypted, $mimetype)
        {
            $final_dir = UploadSession::getFinalDir();

            $filesize = filesize($final_dir . '/' . $filename);
            unlink($final_dir . '/' . $filename);

            $file = new FileModel
            (
                fullpath_encrypted: $filename_encrypted, 
                mimetype:           $mimetype, 
                size:               $filesize, 
                id_user:            $_SESSION['ID_USER']
            );

            $file_transfer = new FileTransferModel
            (
                transfer_type: FileTransferModel::TTYPE_UPLOAD,
                id_file:       $file->getFileID()
            );

            mypdo::connect('insert');
            mypdo::beginTransaction();

            if ($file->ins()===true && $file_transfer->ins()===true)
                mypdo::commit();
            else
                mypdo::rollBack();
        }

        private static function processFileEncryption($filename, &$filename_encrypted)
        {
            $final_dir = UploadSession::getFinalDir();

            $cipherkey = UserKeysController::getCipherKey();
            
            $filename_encrypted = Crypto::encrypt($filename, $cipherkey, Crypto::HEX);

            Crypto::encryptFile
            (
                source: $final_dir . '/' . $filename, 
                dest:   $final_dir . '/' . $filename_encrypted, 
                key:    $cipherkey
            );
        }

        public static function storeChunk($filename, $filename_php, $chunk_index, $chunk_size)
        {
            $chunk_dir = UploadSession::getChunkDir($filename);

            if (!is_dir($chunk_dir)) 
                mkdir($chunk_dir);

            $chunk_filename = $chunk_dir . '/part.' . $chunk_index;
            
            move_uploaded_file
            (
                $filename_php, 
                $chunk_filename
            );

            UploadSession::increaseUsedSpace($chunk_size);
        }

        public static function concatChunks($filename)
        {   
            $chunk_dir = UploadSession::getChunkDir($filename);

            $file_chunks = $chunk_dir . "/part.*";
            $file_chunks = glob($file_chunks);   // Array (part.1, part.2, ..., part.n)
            
            sort($file_chunks, SORT_NATURAL);
            
            $final_dir = UploadSession::getFinalDir();

            $final_file = fopen($final_dir . "/" . $filename, 'w');

            foreach ($file_chunks as $file_chunk) 
            {
                $file_chunk_ctx = file_get_contents($file_chunk);
                fwrite($final_file, $file_chunk_ctx);
                unlink($file_chunk);  
            }

            fclose($final_file);

            FileSysHandler::deleteDir($chunk_dir);   // storage/[USER_DIR]/.uploads_buffer/[SESSION_UPLOADS_ID]/[FILENAME]/
        }
    }

?>
