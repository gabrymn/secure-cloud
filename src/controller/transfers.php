<?php   

    require_once __DIR__ . '/../view/assets/navbar.php';

    class TransfersController
    {
        public static function renderTransfersPage()
        {
            $navbar = Navbar::getPrivate('transfers');
            include __DIR__ . '/../view/transfers.php';
        }
    }

?>