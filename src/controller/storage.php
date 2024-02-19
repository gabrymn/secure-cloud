<?php

    require_once __DIR__ . '/../view/assets/navbar.php';

    class StorageController
    {
        public static function render_storage_page()
        {
            $navbar = Navbar::getPrivate('storage');
            include __DIR__ . '/../view/storage.php';
        }
    }

?>