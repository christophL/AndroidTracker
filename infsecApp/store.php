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

	//check whether IMEI is registered
	$query = "SELECT * FROM users WHERE IMEI=$imei";
	$result = $conn->query($query);
	if($result->num_rows != 1) {
		die("Error: given IMEI is not registered!");
	}

	$query = "INSERT INTO coordinates (IMEI, LATITUDE, LONGITUDE) VALUES ($imei, $lat, $long)";

	if ($conn->query($query) === TRUE) {
		echo "New record created successfully";
	} else {
		die("Error: no new record could be created!");
	}

	$query = "SELECT TO_LOCK, NEW_PHONE_PW FROM users WHERE IMEI=$imei";
	$result = $conn->query($query);
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$to_lock = $row["TO_LOCK"];
		$new_pw = $row["NEW_PHONE_PW"];
	} else {
		die("Error: more than one or no user for one IMEI!");
	}
	$conn->close();

	//send http response
	if($to_lock == TRUE) {
		$response['200'] = array(
			'cmd' => 'lock',
			'data' => $new_pw
		);
		$encoded = json_encode($response);
		header('Content-type: application/json');
		exit($encoded);
	}
	
?>
