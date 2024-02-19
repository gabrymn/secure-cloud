<?php   

    require_once __DIR__ . '/../view/assets/navbar.php';

    class TransfersController
    {
        public static function render_transfers_page()
        {
            $navbar = Navbar::getPrivate('transfers');
            include __DIR__ . '/../view/transfers.php';
        }
    }

?>