<?php

    require_once __DIR__ . '/../view/assets/navbar.php';

    class StaticPagesController
    {
        public static function renderPage($page)
        {
            $navbar = Navbar::getPublic('home');
            include __DIR__ . "/../view/static/{$page}.php";
        }
    }

?>