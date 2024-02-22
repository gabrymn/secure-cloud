<?php

    require_once __DIR__ . '/../model/file.php';
    require_once __DIR__ . '/../../resource/crypto.php';

    class FileController
    {
        public static function upload_chunk($temp_dir, $filename, $index, $tmp_name)
        {
            if (!is_dir($temp_dir)) 
                mkdir($temp_dir);

            move_uploaded_file
            (
                $tmp_name, 
                $temp_dir . "/" . $filename . ".part." . $index
            );
        }

        public static function gather_uploaded_chunks($temp_dir, $final_dir, $filename)
        {
            $filePath = $temp_dir . "/" . $filename . ".part.*";
                
            $fileParts = glob($filePath);
            
            sort($fileParts, SORT_NATURAL);

            if (!is_dir($final_dir)) 
                mkdir($final_dir);

            $finalFile = fopen($final_dir . "/" . $filename, 'w');

            foreach ($fileParts as $filePart) 
            {
                $chunk = file_get_contents($filePart);
                fwrite($finalFile, $chunk);
                unlink($filePart);  
            }

            FileSysHandler::rm_dir($temp_dir);

            fclose($finalFile);
        }

        public static function encrypt_and_store_uploaded_files($files_php)
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
        }

        public static function get_file_names_of($id_user)
        {
            $key = "KEY_HERE";

            $file_names = FileModel::sel_file_names_from_id_user($id_user);

            for($i=0; $i<count($file_names); $i++)
            {
                $file_name = crypto::decrypt($file_names[$i], $key);

                if (in_array($file_name, $file_names))
                {
                    self::handle_filename_exists($file_name);
                }

                $file_names[$i] = $file_name;
            }

            return $file_names;
        }

        private static function handle_filename_exists(string &$filename)
        {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);

            $filename = "";

            if ($name)
            {
                $filename .= $name;
                $filename .= '_';
                $filename .= uniqid();
    
                if ($ext)
                    $filename .= '.'.$ext;
            }
            else
            {
                $filename .= '.'.$ext;
                $filename .= '_';
                $filename .= uniqid();
            }

            return $filename;
        }
    }

?>