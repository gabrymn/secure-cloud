<?php

    require_once __DIR__ . '/../../resource/http/http_response.php';
    require_once __DIR__ . '/../../resource/http/upload_session.php';
    require_once __DIR__ . '/../../resource/storage/mypdo.php';
    require_once __DIR__ . '/../../resource/storage/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/security/crypto.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/file_transfer.php';
    require_once __DIR__ . '/user_keys.php';

    class FileUploaderController
    {
        private const ROOT_STORAGE_DIR = __DIR__ . '/../../storage';

        /**
         * Initialize an upload session for a user with the specified upload space requirements.
         *
         * @param array $args - An associative array containing the following parameters:
         *   - 'upload_space_required' (int): The amount of storage space required for the upload session.
         *
         * @return void
         */
        public static function initializeUploadSession($upload_space_required)
        {
            $user = new UserModel(id_user: $_SESSION['ID_USER']);

            if ($user->enoughStorage($upload_space_required) === 0)
            {
                httpResponse::clientError(400, "Storage space available exceeded, delete files first");
            }
            
            UploadSession::initialize($upload_space_required);

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


        /**
         * Handles the streaming upload of a file chunk, processes and stores the chunk,
         * and manages the completion of the overall file upload process.
         *
         * @param array $args - An associative array containing the following parameters:
         *   - 'upload_session_id' (string): The unique identifier for the upload session.
         *   - 'filename' (string): The original filename of the uploaded file.
         *   - 'file' (array): An array containing information about the uploaded chunk:
         *      - 'tmp_name' (string): The temporary filename of the uploaded chunk.
         *      - 'size' (int): The size of the file chunk.
         *   - 'chunk_index' (int): The index of the current file chunk uploaded.
         *   - 'chunks_len' (int): The total number of chunks expected for the file.
         *   - 'mimetype' (string): The MIME type of file.
         *
         * @return void
         */
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
                filename:     $args['filename'], 
                filename_php: $args['file']['tmp_name'], 
                chunk_index:  $args['chunk_index'],
                chunk_size:   $args['file']['size']
            );

            $response_msg = "Chunk uploaded";

            $response_array = ['filename' => $args['filename']];


            // This if is true when a single file is 100% uploaded.
            if (UploadSession::fileUploadIsCompleted($args['filename'], $args['chunks_len']))
            {
                self::concatChunks($args['filename']);
                self::processFileEncryption($args['filename'], $filename_encrypted);
                self::storeFileMetaData($args['filename'], $filename_encrypted, $args['mimetype'], $fileid);

                $response_array['fileid'] = $fileid;

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

            httpResponse::successful(200, $response_msg, $response_array);
        }


        /**
         * Store file metadata in the database after an upload operation.
         *
         * @param string $filename - The original filename.
         * @param string $filename_encrypted - The encrypted version of the filename.
         * @param string $mimetype - The MIME type of the file.
         *
         * @return bool
         */
        private static function storeFileMetaData($filename, $filename_encrypted, $mimetype, &$fileid) : bool
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
            {
                $fileid = $file->getFileID();
                mypdo::commit();
                return true;
            }
            else
            {
                mypdo::rollBack();
                return false;
            }
        }



        /**
         * Process file encryption for a given filename using AES-256-GCM algorithm.
         *
         * @param string $filename - The original filename of the file to be encrypted.
         * @param string &$filename_encrypted - Reference variable to store the encrypted filename.
         *
         * @return bool
         */
        private static function processFileEncryption($filename, &$filename_encrypted) : bool
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

            return true;
        }


        /**
         * Store an uploaded file chunk in the designated directory.
         *
         * @param string $filename - The original filename of the uploaded file.
         * @param string $filename_php - The temporary filename of the uploaded file on the server.
         * @param int $chunk_index - The index of the current file chunk.
         * @param int $chunk_size - The size of the file chunk.
         *
         * @return bool
         */
        public static function storeChunk($filename, $filename_php, $chunk_index, $chunk_size) : bool
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

            return true;
        }


        /**
         * Concatenate and assemble file chunks into the final uploaded file.
         *
         * @param string $filename - The original filename of the uploaded file.
         *
         * @return bool
         */
        public static function concatChunks($filename) : bool
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

            return true;
        }
    }

?>
