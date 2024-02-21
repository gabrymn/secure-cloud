<?php

    class FileTransfer
    {
        private $transfer_datetime;
        private string $transfer_type;
        private string $id_file;

        private const DEFAULT_STR = "DEFAULT_STR_VALUE";

        public function __construct($transfer_datetime = null, string $transfer_type = null, string $id_file = null) 
        {
            $this->set_transfer_datetime($transfer_datetime ? $transfer_datetime : self::DEFAULT_STR);
            $this->set_transfer_type($transfer_type ? $transfer_type : self::DEFAULT_STR);
            $this->set_id_file($id_file ? $id_file : self::DEFAULT_STR);
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
    }

?>