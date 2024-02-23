<?php

    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/file_transfer.php';

    class FileUploaderController
    {
        private const UPLOAD_SESSION_ID_LEN = 32;
        private const UPLOAD_CHUNK_SIZE = 1000000;  // 1MB
        private const UPLOADS_TEMP_DIR = __DIR__ . '/../../storage/uploads_temp_dir';

        public static function initialize_upload_session($args)
        {
            // SPACE_LIMIT = UserModel::select_limit_space_from_id_user()
            define('SPACE_LIMIT', 19283238);

            if ($args['upload_space_required'] >= SPACE_LIMIT)
                http_response::client_error(400, "Storage space available exceeded, delete files first");
            
            if (!isset($_SESSION['upload_sessions']))
                $_SESSION['upload_sessions'] = [];

            if (!is_dir(self::UPLOADS_TEMP_DIR))
                mkdir(self::UPLOADS_TEMP_DIR);

            $rnd_str = new CryptoRNDString();
            $upload_session_id = $rnd_str->generate(self::UPLOAD_SESSION_ID_LEN);

            $_SESSION['upload_sessions'][$upload_session_id] = [];
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_used'] = 0;
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit'] = $args['upload_space_required'];

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $_SESSION['user_storage_dir'] = FileSysHandler::get_user_storage_dir($user->get_id_user(), $user->get_email());

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

        public static function handle_filechunk($args)
        {   
            self::check_session_used_space($args['upload_session_id'], $args['file']['size']);

            $temp_dir = self::get_session_temp_dir($args['upload_session_id'], $args['filename']);

            self::store_chunk($temp_dir, $args['index'], $args['file']['tmp_name']);
            self::increase_session_used_space($args['upload_session_id'], $args['file']['size']);

            if (FileSysHandler::count_files_of($temp_dir) == $args['chunks_len'])
            {
                self::concat_chunks($temp_dir, $args['filename']);
                self::check_session_end($args['upload_session_id']);
            }

            http_response::successful(200);
        }

        private static function check_session_used_space($upload_session_id, $chunk_size)
        {
            $space_used = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_used']);
            $space_limit = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit']);
            $chunk_size = intval($chunk_size);

            if ($space_used + $chunk_size > $space_limit)
            {
                self::destroy_upload_session($upload_session_id);
                http_response::client_error(400, "Upload space limit exeeded");
            }
        }

        private static function check_session_end($upload_session_id)
        {
            $space_used = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_used']);
            $space_limit = intval($_SESSION['upload_sessions'][$upload_session_id]['upload_space_limit']);

            if ($space_used === $space_limit)
            {
                self::destroy_upload_session($upload_session_id);
            }
        }

        public static function destroy_upload_session($upload_session_id)
        {
            unset($_SESSION['upload_sessions'][$upload_session_id]);
            unset($_SESSION['user_storage_dir']);

            $session_upload_dir = self::UPLOADS_TEMP_DIR . '/' . $upload_session_id;

            FileSysHandler::rm_dir($session_upload_dir);
        }

        private static function increase_session_used_space($upload_session_id, $chunk_size)
        {
            $_SESSION['upload_sessions'][$upload_session_id]['upload_space_used'] += $chunk_size; 
        }

        private static function get_session_temp_dir($upload_session_id, $filename)
        {
            $upload_session_root_dir = self::UPLOADS_TEMP_DIR . '/' . $upload_session_id;;

            if (!is_dir($upload_session_root_dir))
                mkdir($upload_session_root_dir);

            return $upload_session_root_dir . '/' . $filename;
        }

        public static function store_chunk($temp_dir, $index, $file_tmpname)
        {
            if (!is_dir($temp_dir)) 
                mkdir($temp_dir);

            $file_chunk_path = $temp_dir . "/part." . $index;

            move_uploaded_file
            (
                $file_tmpname, 
                $file_chunk_path
            );
        }

        public static function concat_chunks($temp_dir, $filename)
        {   
            $file_path = $temp_dir . "/part.*";
            $file_parts = glob($file_path);   // Array (part.1, part.2, ..., part.n)
            sort($file_parts, SORT_NATURAL);
            
            $final_dir = $_SESSION['user_storage_dir'];
            $final_file = fopen($final_dir . "/" . $filename, 'w');

            foreach ($file_parts as $file_part) 
            {
                $chunk = file_get_contents($file_part);
                fwrite($final_file, $chunk);
                unlink($file_part);  
            }

            fclose($final_file);

            FileSysHandler::rm_dir($temp_dir);   // storage/uploads_temp_dir/[SESSION_UPLOADS_ID]/[FILENAME]/
        }

        /*public static function encrypt_uploaded_files($files_php)
        {
            $ckey = "KEY_HERE";

            $user = new UserModel(id_user: $_SESSION['ID_USER']);
            $user->sel_email_from_id();

            $root_dir = FileSysHandler::get_user_storage_dir($user->get_id_user(), $user->get_email());

            $rnd_str = new CryptoRNDString();

            foreach ($files_php as $file_php)
            {
                $id_file = $rnd_str->generate(FileModel::ID_FILE_LEN);

                $file_name_encrypted = crypto::encrypt($file_php['name'], $ckey, crypto::HEX);

                mypdo::connect('insert');
                mypdo::begin_transaction();

                $file = new FileModel
                (
                    id_file:$id_file, 
                    full_path: $file_name_encrypted, 
                    size: $file_php['size'], 
                    mime_type: $file_php['type'], 
                    id_user: $user->get_id_user()
                );

                $file_transfer = new FileTransferModel
                (
                    transfer_type: "upload",
                    id_file: $id_file
                );

                if ($file->ins() && $file_transfer->ins())
                {
                    mypdo::commit();
                }
                else
                {
                    mypdo::roll_back();
                }

                $full_path = $root_dir . '/' .  $file_name_encrypted;   

                crypto::encryptFile($file_php['tmp_name'], $full_path, $ckey);
                unlink($file_php['tmp_name']);
            }

            return true;
        }*/
    }

?>
