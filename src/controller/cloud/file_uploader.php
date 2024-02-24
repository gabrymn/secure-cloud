<?php

    require_once __DIR__ . '/../../../resource/storage/file_system_handler.php';
    require_once __DIR__ . '/../../../resource/http/http_response.php';
    require_once __DIR__ . '/../../../resource/security/crypto.php';
    require_once __DIR__ . '/../../../resource/security/crypto_rnd_string.php';
    require_once __DIR__ . '/../../../resource/storage/mypdo.php';
    require_once __DIR__ . '/../../model/user.php';
    require_once __DIR__ . '/../../model/file.php';
    require_once __DIR__ . '/../../model/file_transfer.php';

    class FileUploaderController
    {
        private const UPLOAD_SESSION_ID_LEN = 32;
        private const UPLOAD_CHUNK_SIZE = 1000000 ;  // 1MB
        private const STORAGE_ROOT_DIR = __DIR__ . '/../../../storage';

        public static function initialize_upload_session($args)
        {
            // SPACE_LIMIT = UserModel::select_limit_space_from_id_user()
            define('SPACE_LIMIT', 19283238919111);

            if ($args['upload_space_required'] >= SPACE_LIMIT)
                http_response::client_error(400, "Storage space available exceeded, delete files first");
            
            if (!isset($_SESSION['upload_sessions']))
                $_SESSION['upload_sessions'] = [];

            $rnd_str = new CryptoRNDString();
            $upload_session_id = $rnd_str->generate(self::UPLOAD_SESSION_ID_LEN);

            $_SESSION['upload_sessions'][$upload_session_id] = [];
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_used'] = 0;
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit'] = $args['upload_space_required'];

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $_SESSION['user_dir'] = FileSysHandler::get_user_dir($user->get_id_user(), $user->get_email());

            self::create_session_dir($upload_session_id);

            http_response::successful
            (
                200, 
                false, 
                array
                (
                    "upload_session_id" => $upload_session_id,
                    "chunk_size" => self::UPLOAD_CHUNK_SIZE 
                )
            );
        }

        public static function handle_upload_streaming($args)
        {   
            self::check_session_id($args['upload_session_id']);
            
            self::check_session_used_space($args['upload_session_id'], $args['file']['size']);
            
            self::store_chunk
            (
                $args['upload_session_id'], 
                $args['filename'], 
                $args['file']['tmp_name'], 
                $args['chunk_index']
            );

            self::increase_session_used_space($args['upload_session_id'], $args['file']['size']);
            
            $chunk_dir = self::get_chunk_dir($args['upload_session_id'], $args['filename']);

            if (FileSysHandler::count_files_of($chunk_dir) == $args['chunks_len'])
            {
                self::concat_chunks($args['upload_session_id'], $args['filename']);
                self::check_possible_session_end($args['upload_session_id']);
            }

            http_response::successful(200);
        }

        private static function check_session_id($upload_session_id)
        {
            if (!in_array($upload_session_id, array_keys($_SESSION['upload_sessions'])))
            {
                http_response::client_error(400, "Invalid upload-session ID");
            }
        }

        private static function check_session_used_space($upload_session_id, $chunk_size)
        {
            $space_used = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_used']);
            $space_limit = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit']);
            $chunk_size = intval($chunk_size);

            if ($space_used + $chunk_size > $space_limit)
            {
                self::destroy_session($upload_session_id);
                http_response::client_error(400, "Upload space limit exeeded");
            }
        }

        private static function check_possible_session_end($upload_session_id)
        {
            $space_used = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_used']);
            $space_limit = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit']);

            if ($space_used === $space_limit)
            {
                self::destroy_session($upload_session_id);
                http_response::successful(200, "Upload completed");
            }
        }

        public static function destroy_session($upload_session_id)
        {
            FileSysHandler::rm_dir(self::get_session_dir($upload_session_id));

            unset($_SESSION['upload_sessions'][$upload_session_id]);
            unset($_SESSION['user_dir']);
        }

        private static function increase_session_used_space($upload_session_id, $chunk_size)
        {
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_used'] += $chunk_size; 
        }

        private static function create_session_dir($upload_session_id)
        {
            $upload_session_dir = self::STORAGE_ROOT_DIR . '/';
            $upload_session_dir .= $_SESSION['user_dir'] . '/';
            $upload_session_dir .= FileSysHandler::UPLOADS_DIRNAME . '/';
            $upload_session_dir .= $upload_session_id;

            if (!is_dir($upload_session_dir))
                mkdir($upload_session_dir);

            $_SESSION['upload_sessions'][$upload_session_id]['buffer_dir'] = $upload_session_dir;

            // example output: "storage / [USER_DIR] / .uploads_buffer / [UPLOAD_SESSION_ID]"
        }

        private static function get_session_dir($upload_session_id)
        {
            return $_SESSION['upload_sessions'][$upload_session_id]['buffer_dir'];
        }

        private static function get_chunk_dir($upload_session_id, $filename)
        {
            return self::get_session_dir($upload_session_id) . '/' . $filename;
        }

        private static function get_final_dir()
        {
            $final_dir = self::STORAGE_ROOT_DIR . '/';
            $final_dir .= $_SESSION['user_dir'] . '/';
            $final_dir .= FileSysHandler::DATA_DIRNAME . '/';

            return  $final_dir;
        }

        public static function store_chunk($upload_session_id, $filename, $filename_php, $index)
        {
            $chunk_dir = self::get_chunk_dir($upload_session_id, $filename);

            if (!is_dir($chunk_dir)) 
                mkdir($chunk_dir);

            $chunk_filename = $chunk_dir . '/part.' . $index;
            
            move_uploaded_file
            (
                $filename_php, 
                $chunk_filename
            );
        }

        public static function concat_chunks($upload_session_id, $filename)
        {   
            $chunk_dir = self::get_chunk_dir($upload_session_id, $filename);

            $file_chunks = $chunk_dir . "/part.*";
            $file_chunks = glob($file_chunks);   // Array (part.1, part.2, ..., part.n)
            
            sort($file_chunks, SORT_NATURAL);
            
            $final_dir = self::get_final_dir();

            $final_file = fopen($final_dir . "/" . $filename, 'w');

            foreach ($file_chunks as $file_chunk) 
            {
                $file_chunk_ctx = file_get_contents($file_chunk);
                fwrite($final_file, $file_chunk_ctx);
                unlink($file_chunk);  
            }

            fclose($final_file);

            FileSysHandler::rm_dir($chunk_dir);   // storage/[USER_DIR]/.uploads_buffer/[SESSION_UPLOADS_ID]/[FILENAME]/
        }
    }

?>
