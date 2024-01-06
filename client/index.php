<?php

    require_once("../server/class/token.php");
    $x = token::gen();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME PAGE</title>
</head>
<body>
    <h3><?php echo $x; ?></h3>
</body>
</html>