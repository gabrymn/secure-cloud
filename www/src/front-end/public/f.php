<?php
    require_once "backend-dir.php";
    require_once __BACKEND__ . "class/sqlc.php";

    $files = scandir(__BACKEND__ . "users");
    var_dump($files);

    //sqlc::init_db();
?>
