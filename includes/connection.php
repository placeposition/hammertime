<?php

	function pdo_connection() {

		$httphost = $_SERVER['HTTP_HOST'];

		if(strpos($httphost, 'local') !== FALSE) {

			//local database
			$host = "localhost";
			$password = "empireb";
			$username = "root";
			$dbname = "underthehammer_db";

		} else {

			//PRODUCTION VALUES
		}

		$result = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

		if (!$result) {
			throw new Exception ($host.$dbname.'Could not connect to database server');
		} else {
			$result->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$result->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			$result->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
			return $result;
		}
	}

?>
