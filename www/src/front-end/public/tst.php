<?php
    require_once 'backend-dir.php';
    require_once __BACKEND__ . 'class/sqlc.php';    
    require_once __BACKEND__ . 'class/email.php';
    echo email_is_real("pippo@paperino.gov");
?>