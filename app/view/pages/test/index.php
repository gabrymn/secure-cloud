<?php
    define('__ROOT__', '../../../');
    require_once __ROOT__ . 'model/ds/twoFactorAuth.php';

    $x = new TFAuth();

    echo "<img src=" . $x->get_qrcode_url(). ">";
    
?>