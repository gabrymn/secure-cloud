<?php

    class myfile {

        private $tmp;
        private $size;              // file size
        private $path;           // full path
        private $name;           // name
        private $extension;      // extension
        private $filename;       // = name.extension
        private $filedir;
        
        public function __construct(string $filename, string $filedir, string $path, string $size, string $tmp) {
            
            $this->filedir = $filedir;
            $this->tmp = $tmp;
            $this->size = $size;
            $this->path = $path;

            $str = $filename;
            $rp = explode('.', strrev($str), 2);

            $this->extension = strtolower(strrev($rp[0]));
            $this->name = strrev($rp[1]);
            $this->filename = $filename;

            $this->handle_exists();
        }

        public function get($prop){
            switch ($prop) {
                case 'SIZE': return $this->size;
                case 'PATH': return $this->path;
                case 'NAME': return $this->name;
                case 'EXTENSION': return $this->extension;
                case 'FILENAME': return $this->filename;
                case 'TMP': return $this->tmp;
                case 'TYPE': return $this->type;
                default: return null;
            }
        }

        private function handle_exists(){

            if (file_exists($this->path)){
                $add = 1;
                while (file_exists($this->path)) {
                    $this->path = 'users_data/'.$_SESSION['USERNAME'].'/'.$this->name."_".strval($add).'.'.$this->extension;
                    $add++;
                }
                $this->name = $this->name."_".strval($add-1);
                $this->filename = $this->name.'.'.$this->extension;
            }
        }

        public function upload(){
            move_uploaded_file($this->tmp, $this->path);
            unlink($this->tmp);
        }

        public function encrypt(){
            $img_ctx = file_get_contents($this->path);
            $encrypted_file_data = AES::encrypt($img_ctx, 1);
            file_put_contents($this->path, $encrypted_file_data);
        }

        public function decrypt(){
            $encrypted = file_get_contents($this->path);
            $decrypted_file_data = AES::decrypt($encrypted, 1);
            file_put_contents($this->path, $decrypted_file_data);
        }
    }


?>