<?php

    require_once __DIR__ . '/../view/assets/navbar.php';

    class StorageController
    {
        public static function renderStoragePage()
        {
            $navbar = Navbar::getPrivate('storage');
            include __DIR__ . '/../view/storage.php';
        }
    }

?>