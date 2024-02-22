<?php

    require_once __DIR__ . '/../model/user_security.php';
    require_once __DIR__ . '/../model/user.php';
    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../model/file_transfer.php';
    require_once __DIR__ . '/../../resource/crypto.php';
    require_once __DIR__ . '/../../resource/http_response.php';
    require_once __DIR__ . '/../../resource/file_sys_handler.php';
    require_once __DIR__ . '/../../resource/crypto_rnd_string.php';
    require_once __DIR__ . '/../../resource/user_keys_handler.php';
    require_once __DIR__ . '/../../resource/mydatetime.php';
    require_once __DIR__ . '/../view/assets/navbar.php';
    require_once __DIR__ . '/file.php';
    require_once __DIR__ . '/userkeys.php';

    class CloudDriveController
    {
        private const TEMP_DIR = 'TEMP_DIR';
        private const FINAL_DIR = 'FINAL_DIR';

        public static function render_clouddrive_page()
        {
            //$file_names = FileController::get_file_names_of($_SESSION['ID_USER']);

            $navbar = Navbar::getPrivate('clouddrive');

            include __DIR__ . '/../view/clouddrive.php';
        }
        
        /*
        public static function upload_files($files_php)
        {       
            // files_sum_size = FileController::get_size_of(array_column(files_php, 'size'))
            
            // if files_sum_size >= MAX_SPACE
            //     => error 400

            // status = FileController::store_files(files_php)
            
            // if (!status)
            //     => error 500
            
            // => successful 200
        }*/

        public static function check_space($args)
        {
            if ($args['request_space'] >= 111111)
            {
                http_response::client_error(400);
            }
            else
            {
                http_response::successful(200);
            }
        }

        public static function upload($args)
        {   
            FileController::upload_chunk(self::TEMP_DIR, $args['filename'], $args['index'], $args['file']['tmp_name']);

            if (FileSysHandler::count_files_of(self::TEMP_DIR) == $args['chunks_n'])
            {
                FileController::gather_uploaded_chunks(self::TEMP_DIR, self::FINAL_DIR, $args['filename']);
            }

            http_response::successful(200);
        }

        public static function download_files(array $id_files)
        {
            
        }

        public static function render_file_view()
        {

        }
    }

?>