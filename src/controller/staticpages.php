<?php


    class StaticPagesController
    {
        public static function render_home()
        {       
            include __DIR__ . '/../view/static/home.html';
        }
    }

?>