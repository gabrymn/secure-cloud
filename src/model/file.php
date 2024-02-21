<?php

    require_once __DIR__ . '/model.php';

    class File extends Model
    {
        private string $id_file;
        private string $full_path;
        private int $size;
        private int $id_user;

        public function __construct(?string $id_file = null, ?string $full_path = null, ?int $size = null, ?int $id_user = null) 
        {
            $this->set_id_file($id_file ? $id_file : parent::DEFAULT_STR);
            $this->set_full_path($full_path ? $full_path : parent::DEFAULT_STR);
            $this->set_size($size ? $size : parent::DEFAULT_INT);
            $this->set_id_user($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function set_id_file(?string $id_file) : void
        {
            $this->id_file = $id_file;
        }

        public function get_id_file() : string
        {
            return $this->id_file;
        }

        public function set_full_path(?string $full_path) : void
        {
            $this->full_path = $full_path ? $full_path : self::DEFAULT_STR;
        }

        public function get_full_path() : string
        {
            return $this->full_path;
        }

        public function set_size(?int $size) : void
        {
            $this->size = $size ? $size : self::DEFAULT_INT;
        }

        public function get_size() : int
        {
            return $this->size;
        }

        public function set_id_user(?int $id_user) : void
        {
            $this->id_user = $id_user ? $id_user : self::DEFAULT_INT;
        }

        public function get_id_user() : int
        {
            return $this->id_user;
        }

        public function to_assoc_array()
        {
            
        }

        public function ins()
        {
            
        }
    }

?>