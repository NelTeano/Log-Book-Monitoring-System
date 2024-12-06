<?php

// $dbservername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "health_database";

//create connection
// $conn = new mysqli ($dbservername, $username, $password, $dbname);
// //Check connection
// if ($conn->connect_error) {
//     die("Connection failed:".$conn->connect_error);
// }
// else{
//      echo "Connected Successfully";

class Config {
		public function __construct(){
			$dbservername = 'localhost';
			$username = 'root';
			$password = '';
			$dbname = 'logbookdb';
			
			$conn = new mysqli($dbservername,$username,$password,$dbname);

			
			$this->conn = $conn;

	}
 }

?>