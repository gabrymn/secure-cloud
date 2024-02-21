<?php

    require_once __DIR__ . '/model.php';

    class FileTransfer extends Model
    {
        private $transfer_datetime;
        private string $transfer_type;
        private string $id_file;

        public function __construct($transfer_datetime = null, string $transfer_type = null, string $id_file = null) 
        {
            $this->set_transfer_datetime($transfer_datetime ? $transfer_datetime : parent::DEFAULT_STR);
            $this->set_transfer_type($transfer_type ? $transfer_type : parent::DEFAULT_STR);
            $this->set_id_file($id_file ? $id_file : parent::DEFAULT_STR);
        }

        public function set_transfer_datetime($transfer_datetime = null) : void
        {
            $this->transfer_datetime = $transfer_datetime ? $transfer_datetime : "FUNZIONE_DATETIME_NOW()";
        }

        public function get_transfer_datetime()
        {
            return $this->transfer_datetime;
        }

        public function set_transfer_type(?string $transfer_type) : void
        {
            $this->transfer_type = $transfer_type ? $transfer_type : self::DEFAULT_STR;
        }

        public function get_transfer_type() : string
        {
            return $this->transfer_type;
        }

        public function set_id_file(?string $id_file) : void
        {
            $this->id_file = $id_file ? $id_file : self::DEFAULT_STR;
        }

        public function get_id_file() : string
        {
            return $this->id_file;
        }

        public function to_assoc_array()
        {
            
        }

        public function ins()
        {

        }
    }

?>