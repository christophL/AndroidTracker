<?php
	//call example:
	//http://localhost/infsecApp/store.php?IMEI=1234567890&LAT=34.9999&LONG=23.4444
	$imei = htmlspecialchars($_POST["IMEI"]);
	$lat = htmlspecialchars($_POST["LAT"]);
	$long = htmlspecialchars($_POST["LONG"]);

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "infsecApp";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
 	   die("Connection failed: " . $conn->connect_error);
	} 

	$query = "INSERT INTO coordinates (IMEI, LATITUDE, LONGITUDE) VALUES ($imei, $lat, $long)";

	if ($conn->query($query) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $query . "<br>" . $conn->error;
	}

	$conn->close();
	
?>
