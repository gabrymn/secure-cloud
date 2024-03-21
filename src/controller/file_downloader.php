<?php

    class FileDownloaderController
    {
        private const ROOT_STORAGE_DIR = __DIR__ . '/../../storage';
        public const DOWNLOAD_API_URL = '/clouddrive/download?fileid=';

        /**
         * Processes the request to download a file.
         *
         * This method handles the decryption and download of an encrypted file.
         * Checks are performed for the existence of the file, correct decryption,
         * and finally, the file is downloaded. After the download, the temporary 
         * plain-text file is deleted, and the transfer operation is logged in the 
         * database
         *
         * @param string $id_file The unique identifier of the file to be downloaded.
         *
         * @return void
         */
        public static function processDownload(string $id_file)
        {
            $filename_encrypted = self::getEncryptedFilename($id_file);
            
            if ($filename_encrypted === false)
                HttpResponse::serverError();

            if ($filename_encrypted === -1)
                HttpResponse::clientError(404, "The requested file does not exist or is currently unavailable.");

            $cipher_key = UserKeysController::getCipherKey();

            $filename_plaintext = Crypto::decrypt($filename_encrypted, $cipher_key);

            $source_filepath = self::getSourceFilepath($filename_encrypted);
            $download_filepath = self::getDownloadFilepath($filename_plaintext);

            Crypto::decryptFile($source_filepath, $download_filepath, $cipher_key);

            self::storeFileTransfer($id_file);

            httpResponse::download($download_filepath);
            unlink($download_filepath);
        }


        /**
         * Gets the encrypted filename associated with the specified file ID.
         *
         * This method retrieves the encrypted filename for a given file ID and user ID
         * by querying the database using the FileModel class.
         *
         * @param string $id_file The unique identifier of the file.
         *
         * @return string|int|false The encrypted filename if found, -1 if not found, or false for an error.
         */
        private static function getEncryptedFilename(string $id_file) : string|int|false
        {
            $file = new FileModel(id_file: $id_file, id_user: $_SESSION['ID_USER']);

            $filename_encrypted = $file->sel_fileName_By_userID_fileID();

            return $filename_encrypted;
        }

        /**
         * Gets the source filepath for the encrypted filename.
         *
         * This method constructs the source filepath for a given encrypted filename by combining
         * the root storage directory, user-specific directory, data directory, and the encrypted filename.
         *
         * @param string $filename_encrypted The encrypted filename for which the source filepath is needed.
         *
         * @return string The constructed source filepath.
         */
        private static function getSourceFilepath(string $filename_encrypted) : string
        {
            $source_filepath = self::ROOT_STORAGE_DIR .'/';
            $source_filepath .= $_SESSION['USER_DIR'] . '/';
            $source_filepath .= UserModel::DATA_DIRNAME . '/'; 
            $source_filepath .= $filename_encrypted; 

            return $source_filepath;
        }

        /**
         * Gets the download filepath for the plaintext filename.
         *
         * This method constructs the download filepath for a given plaintext filename by combining
         * the root storage directory, user-specific directory, downloads buffer directory, and the plaintext 
         * filename.
         *
         * @param string $filename_plaintext The plaintext filename for which the download filepath is needed.
         *
         * @return string The constructed download filepath.
         */
        private static function getDownloadFilepath(string $filename_plaintext) : string
        {
            $download_filepath = self::ROOT_STORAGE_DIR . '/';
            $download_filepath .= $_SESSION['USER_DIR'] . '/';
            $download_filepath .= UserModel::DOWNLOADS_DIRNAME . '/';
            $download_filepath .= $filename_plaintext;

            return $download_filepath;
        }

        /**
         * Stores the file transfer operation in the database.
         *
         * This method creates a new instance of FileTransferModel and inserts a record into the database
         * representing a file transfer operation with the specified file ID and transfer type (download).
         *
         * @param string $id_file The unique identifier of the file involved in the transfer.
         *
         * @return bool True if the file transfer operation was successfully stored; false otherwise.
         */
        private static function storeFileTransfer(string $id_file) : bool
        {
            $file_transfer = new FileTransferModel
            (
                transfer_type: FileTransferModel::TTYPE_DOWNLOAD,
                id_file: $id_file
            );

            return $file_transfer->ins();
        }
    }

?>