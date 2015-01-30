<?php

	$minMetersDiff = 3;

	$imei = htmlspecialchars($_POST["IMEI"]);
	$lat = htmlspecialchars($_POST["LAT"]);
	$long = htmlspecialchars($_POST["LONG"]);
	$acc = htmlspecialchars($_POST["ACC"]);

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

	//check whether new coordinates differ substantially from last received ones
	$query = "SELECT * FROM coordinates WHERE TIME = ( select max(TIME) from coordinates WHERE IMEI=$imei)";
	$result = $conn->query($query);
	$do_insert = TRUE;
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		//formula for computation of distance between two coordinates taken from here
		//(http://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
		$latFrom = $row["LATITUDE"];
		$lonFrom = $row["LONGITUDE"];
		$latTo = $lat;
		$lonTo = $long;
		$earthRadius = 6371000;

		// convert from degrees to radians
  		$latFrom = deg2rad($latFrom);
	  	$lonFrom = deg2rad($lonFrom);
	 	$latTo = deg2rad($latTo);
	 	$lonTo = deg2rad($lonTo);

	  	$lonDelta = $lonTo - $lonFrom;
  		$a = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  		$b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  		$angle = atan2(sqrt($a), $b);
		$meters = $angle * $earthRadius;

		//if new coordinates are more than minMetersDiff located from the old ones
		if($meters <= $minMetersDiff) {
			$do_insert = FALSE;
			//no new record is created BUT the timestamp of the last one is updated
			$lat = $row["LATITUDE"];
			$lon = $row["LONGITUDE"];
			$query = "UPDATE coordinates SET TIME=NOW() WHERE IMEI=$imei AND LATITUDE=$lat AND LONGITUDE=$lon";
			$result = $conn->query($query);
		}
	}
	if($do_insert){
	  $query = "INSERT INTO coordinates (IMEI, LATITUDE, LONGITUDE,ACCURACY) VALUES ($imei, $lat, $long,$acc)";

			if ($conn->query($query) === TRUE) {
				//echo "New record created successfully";
			} else {
				die("Error: no new record could be created!");
			}
	}
	
	//check whether we want to send something back
	$query = "SELECT TO_LOCK, NEW_PHONE_PW, WIPE FROM users WHERE IMEI=$imei";
	$result = $conn->query($query);
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$to_lock = $row["TO_LOCK"];
		$new_pw = $row["NEW_PHONE_PW"];
		$to_wipe = $row["WIPE"];
	} else {
		die("Error: more than one or no user for one IMEI!");
	}
	
	//send http response
	//this either communicates the wish to lock the phone or to wipe it
	//first check if the phone should be wiped
	//if yes, ignore locking up until next response
	if($to_wipe == TRUE) {
		$query = "UPDATE users SET WIPE=0";
		$result = $conn->query($query);

		$response['200'] = array(
			'cmd' => 'wipe'
		);
		$encoded = json_encode($response);
		header('Content-type: application/json');
		$conn->close();
		exit($encoded);
	}

	if($to_lock == TRUE) {
		$query = "UPDATE users SET TO_LOCK=0, NEW_PHONE_PW=''";
		$result = $conn->query($query);

		$response['200'] = array(
			'cmd' => 'lock',
			'data' => $new_pw
		);
		$encoded = json_encode($response);
		header('Content-type: application/json');
		$conn->close();
		exit($encoded);
	}

	$conn->close();
	
?>
