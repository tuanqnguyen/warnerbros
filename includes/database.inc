<?php

function retrieve_database_connection() {

	$server = 'localhost';
	$database = 'crimes';
	$user = 'user';
	$password = 'user';

	// Create connection
	$database_connection = new mysqli($server, $user, $password, $database);

	// Check connection
	if($database_connection->connect_error) {
	  die('Connection failed: ' . $database_connection->connect_error);
	}
	
	// echo 'Connected successfully';
	return $database_connection;
}

?>