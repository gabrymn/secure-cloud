<?php

    require_once __DIR__ . '/model.php';
    require_once __DIR__ . '/../../resource/mydatetime.php';

    class FileTransfer extends Model
    {
        private $transfer_date;
        private string $transfer_type;
        private string $id_file;

        public function __construct($transfer_date = null, string $transfer_type = null, string $id_file = null) 
        {
            $this->set_transfer_date($transfer_date ? $transfer_date : parent::DEFAULT_STR);
            $this->set_transfer_type($transfer_type ? $transfer_type : parent::DEFAULT_STR);
            $this->set_id_file($id_file ? $id_file : parent::DEFAULT_STR);
        }

        public function set_transfer_date($transfer_date = null) : void
        {
            if ($transfer_date === parent::DEFAULT_STR || $transfer_date === null)
            {
                $this->transfer_date = MyDatetime::now();
            }
            else
            {
                $this->transfer_date = $transfer_date;
            }
        }

        public function get_transfer_date()
        {
            return $this->transfer_date;
        }

        public function set_transfer_type(string $transfer_type) : void
        {
            $this->transfer_type = in_array($transfer_type, ['upload', 'download']) ? $transfer_type : self::DEFAULT_STR;
        }

        public function get_transfer_type() : string
        {
            return $this->transfer_type;
        }

        public function set_id_file(string $id_file) : void
        {
            $this->id_file = $id_file;
        }

        public function get_id_file() : string
        {
            return $this->id_file;
        }

        public function to_assoc_array($transfer_date = false, $transfer_type = false, $id_file = false)
        {
            $params = array();

            if ($transfer_date)
                $params['transfer_date'] =  $this->get_transfer_date();

            if ($transfer_type)
                $params['transfer_type'] =  $this->get_transfer_type();

            if ($id_file)
                $params['id_file'] =  $this->get_id_file();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `file_transfers` (`transfer_date`, `transfer_type`, `id_file`) VALUES (:transfer_date, :transfer_type, :id_file)";
        
            mypdo::connect('insert');

            return mypdo::qry_exec($qry, $this->to_assoc_array(transfer_date:true, transfer_type:true, id_file:true));
        }
    }

?>