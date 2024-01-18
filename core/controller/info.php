<?php

	echo "API info page<br>";
	file_put_contents("test.dat", file_get_contents("test.dat") . "\n" . "wecohiehbciwhciewofjcefijcewoicjweichwechiwicjewcwicweicwnweoicw");
	echo file_get_contents("test.dat");
	
?>