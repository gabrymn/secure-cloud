<?php

    require_once __DIR__ . '/../../resource/storage/mypdo.php';
    require_once __DIR__ . '/model.php';

    class FileModel extends Model
    {
        private string $id_file;
        private string $fullpath_encrypted;
        private int $size;
        private string $mimetype;
        private int $id_user;

        public const ID_FILE_LEN = 20;

        public function __construct(string $id_file = null, ?string $fullpath_encrypted = null, ?int $size = null, ?string $mimetype = null, ?int $id_user = null) 
        {
            if ($id_file === null)
                $this->setFileIDRandom();
            else
                $this->setFileID($id_file);

            $this->setFullPathEncrypted($fullpath_encrypted ? $fullpath_encrypted : parent::DEFAULT_STR);
            $this->setSize($size ? $size : parent::DEFAULT_INT);
            $this->setMimeType($mimetype ? $mimetype : parent::DEFAULT_STR);
            $this->setUserID($id_user ? $id_user : parent::DEFAULT_INT);
        }

        public function setFileID(string $id_file) : void
        {
            if ($id_file === parent::DEFAULT_STR || strlen($id_file) !== self::ID_FILE_LEN)
                $this->id_file = parent::DEFAULT_STR;
            else
                $this->id_file = $id_file;
        }

        public function setFileIDRandom()
        {
            $id_file = $this->generateUID(self::ID_FILE_LEN);
            $this->setFileID($id_file);
        }

        public function getFileID() : string
        {
            return $this->id_file;
        }

        public function setFullPathEncrypted(string $fullpath_encrypted) : void
        {
            $this->fullpath_encrypted = $fullpath_encrypted;
        }

        public function getFullPathEncrypted() : string
        {
            return $this->fullpath_encrypted;
        }

        public function setSize(int $size) : void
        {
            $this->size = $size;
        }

        public function getSize() : int
        {
            return $this->size;
        }

        public function setMimeType(string $mimetype) : void
        {
            $this->mimetype = $mimetype;
        }

        public function getMimeType() : string
        {
            return $this->mimetype;
        }

        public function setUserID(int $id_user) : void
        {
            $this->id_user = $id_user ? $id_user : self::DEFAULT_INT;
        }

        public function getUserID() : int
        {
            return $this->id_user;
        }

        public function toAssocArray($id_file = false, $fullpath_encrypted = false, $size = false, $mimetype = false, $id_user = false)
        {
            $params = array();

            if ($id_file)
                $params['id_file'] =  $this->getFileID();

            if ($fullpath_encrypted)
                $params['fullpath_encrypted'] =  $this->getFullPathEncrypted();

            if ($size)
                $params['size'] =  $this->getSize();

            if ($mimetype)
                $params['mimetype'] = $this->getMimeType();

            if ($id_user)
                $params['id_user'] =  $this->getUserID();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `files` (`id_file`, `fullpath_encrypted`, `size`, `mimetype`, `id_user`) VALUES (:id_file, :fullpath_encrypted, :size, :mimetype, :id_user)";

            MyPDO::connect('insert');

            return MyPDO::qryExec($qry, $this->toAssocArray(id_file:true, fullpath_encrypted:true, size:true, mimetype:true, id_user:true));
        }

        public function sel_fileName_by_userID_fileID()
        {
            $qry = "SELECT fullpath_encrypted FROM files WHERE id_user = :id_user AND id_file = :id_file";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $this->toAssocArray(id_user: true, id_file: true));

            if ($res === false)
                return false;
            else if ($res === array())
                return -1;
            else
            {
                $fullpath_encrypted = $res[0]['fullpath_encrypted'];
                return $fullpath_encrypted;
            }
        }

        public static function sel_fileIDs_fileNames_by_userID($id_user)
        {
            $file = new FileModel(id_user: $id_user);

            $qry = "SELECT id_file, fullpath_encrypted FROM files WHERE id_user = :id_user";

            MyPDO::connect('select');

            $res = MyPDO::qryExec($qry, $file->toAssocArray(id_user: true));

            if ($res === false)
                return false;
            else
            {
                $file_data = [];

                foreach ($res as $row) 
                {
                    $file_data[] = 
                    [
                        'id_file' => $row['id_file'],
                        'fullpath_encrypted' => $row['fullpath_encrypted']
                    ];
                }

                return $file_data;
            }
        }
    }

?>