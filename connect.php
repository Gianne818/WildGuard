<?php 
	$connection = new mysqli('127.0.0.1', 'root', '', 'dbCruzSanchez');

	if ($connection->connect_error) {
		die('Database connection failed: ' . $connection->connect_error);
	}
		
?>