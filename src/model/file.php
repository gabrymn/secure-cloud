<?php

    require_once __DIR__ . '/../../resource/mypdo.php';
    require_once __DIR__ . '/model.php';

    class FileModel extends Model
    {
        private string $id_file;
        private string $full_path;
        private int $size;
        private string $mime_type;
        private int $id_user;

        public const ID_FILE_LEN = 20;

        public function __construct(?string $id_file = null, ?string $full_path = null, ?int $size = null, ?string $mime_type = null, ?int $id_user = null) 
        {
            $this->set_id_file($id_file ? $id_file : parent::DEFAULT_STR);
            $this->set_full_path($full_path ? $full_path : parent::DEFAULT_STR);
            $this->set_size($size ? $size : parent::DEFAULT_INT);
            $this->set_mime_type($mime_type ? $mime_type : parent::DEFAULT_STR);
            $this->set_id_user($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function set_id_file(string $id_file) : void
        {
            if ($id_file === parent::DEFAULT_STR || strlen($id_file) !== self::ID_FILE_LEN)
                $this->id_file = parent::DEFAULT_STR;
            else
                $this->id_file = $id_file;
        }

        public function get_id_file() : string
        {
            return $this->id_file;
        }

        public function set_full_path(string $full_path) : void
        {
            $this->full_path = $full_path;
        }

        public function get_full_path() : string
        {
            return $this->full_path;
        }

        public function set_size(int $size) : void
        {
            $this->size = $size;
        }

        public function get_size() : int
        {
            return $this->size;
        }

        public function set_mime_type(string $mime_type) : void
        {
            $this->mime_type = $mime_type;
        }

        public function get_mime_type() : string
        {
            return $this->mime_type;
        }

        public function set_id_user(int $id_user) : void
        {
            $this->id_user = $id_user ? $id_user : self::DEFAULT_INT;
        }

        public function get_id_user() : int
        {
            return $this->id_user;
        }

        public function to_assoc_array($id_file = false, $full_path = false, $size = false, $mime_type = false, $id_user = false)
        {
            $params = array();

            if ($id_file)
                $params['id_file'] =  $this->get_id_file();

            if ($full_path)
                $params['full_path'] =  $this->get_full_path();

            if ($size)
                $params['size'] =  $this->get_size();

            if ($mime_type)
                $params['mime_type'] = $this->get_mime_type();

            if ($id_user)
                $params['id_user'] =  $this->get_id_user();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `files` (`id_file`, `full_path`, `size`, `mime_type`, `id_user`) VALUES (:id_file, :full_path, :size, :mime_type, :id_user)";

            mypdo::connect('insert');

            return mypdo::qry_exec($qry, $this->to_assoc_array(id_file:true, full_path:true, size:true, mime_type:true, id_user:true));
        }

        public static function sel_file_names_from_id_user($id_user)
        {
            $file = new FileModel(id_user: $id_user);

            $qry = "SELECT full_path FROM files WHERE id_user = :id_user";

            mypdo::connect('select');

            $res = mypdo::qry_exec($qry, $file->to_assoc_array(id_user: true));

            if ($res === false)
                return false;
            else
            {
                return array_column($res, 'full_path');
            }
        }
    }

?>