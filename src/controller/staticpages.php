<?php


    class StaticPagesController
    {
        public static function render_page($page)
        {
            include __DIR__ . "/../view/static/{$page}.html";
        }
    }

?>