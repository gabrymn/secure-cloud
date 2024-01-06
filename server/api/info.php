<?php

	echo "API info page<br>";
	file_put_contents("ciao.txt", file_get_contents("ciao.txt") . "\n" . "wecohiehbciwhciewofjcefijcewoicjweichwechiwicjewcwicweicwnweoicw");
	echo file_get_contents("ciao.txt");
	
?>