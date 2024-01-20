<?php

    define('__ROOT__', '../../');

    require_once(__ROOT__ . "model/token.php");
    $tkn = new token(-44, ["0-9"]);
    echo $tkn->get() . "<br>";
    $tkn->refresh(64);
    echo $tkn->get();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME PAGE</title>
</head>
<body>
</body>
</html>