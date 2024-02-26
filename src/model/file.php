<?php

    require_once __DIR__ . '/../../resource/storage/MyPDO.php';
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
            $this->setFileID($id_file ? $id_file : parent::DEFAULT_STR);
            $this->setFullPath($full_path ? $full_path : parent::DEFAULT_STR);
            $this->setSize($size ? $size : parent::DEFAULT_INT);
            $this->setMimeType($mime_type ? $mime_type : parent::DEFAULT_STR);
            $this->setUserID($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function setFileID(string $id_file) : void
        {
            if ($id_file === parent::DEFAULT_STR || strlen($id_file) !== self::ID_FILE_LEN)
                $this->id_file = parent::DEFAULT_STR;
            else
                $this->id_file = $id_file;
        }

        public function getFileID() : string
        {
            return $this->id_file;
        }

        public function setFullPath(string $full_path) : void
        {
            $this->full_path = $full_path;
        }

        public function getFullPath() : string
        {
            return $this->full_path;
        }

        public function setSize(int $size) : void
        {
            $this->size = $size;
        }

        public function getSize() : int
        {
            return $this->size;
        }

        public function setMimeType(string $mime_type) : void
        {
            $this->mime_type = $mime_type;
        }

        public function getMimeType() : string
        {
            return $this->mime_type;
        }

        public function setUserID(int $id_user) : void
        {
            $this->id_user = $id_user ? $id_user : self::DEFAULT_INT;
        }

        public function getUserID() : int
        {
            return $this->id_user;
        }

        public function toAssocArray($id_file = false, $full_path = false, $size = false, $mime_type = false, $id_user = false)
        {
            $params = array();

            if ($id_file)
                $params['id_file'] =  $this->getFileID();

            if ($full_path)
                $params['full_path'] =  $this->getFullPath();

            if ($size)
                $params['size'] =  $this->getSize();

            if ($mime_type)
                $params['mime_type'] = $this->getMimeType();

            if ($id_user)
                $params['id_user'] =  $this->getUserID();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `files` (`id_file`, `full_path`, `size`, `mime_type`, `id_user`) VALUES (:id_file, :full_path, :size, :mime_type, :id_user)";

            MyPDO::connect('insert');

            return MyPDO::qryExec($qry, $this->toAssocArray(id_file:true, full_path:true, size:true, mime_type:true, id_user:true));
        }

        public static function selFileNamesFromUserID($id_user)
        {
            $file = new FileModel(id_user: $id_user);

            $qry = "SELECT full_path FROM files WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $file->toAssocArray(id_user: true));

            if ($res === false)
                return false;
            else
            {
                return array_column($res, 'full_path');
            }
        }
    }

?>