<?php

	// Start XML file, create parent node

	$dom = new DOMDocument("1.0");
	$node = $dom->createElement("markers");
	$parnode = $dom->appendChild($node);

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

	// Select all the rows in the markers table with the given IMEI
	$imei = htmlspecialchars($_GET["IMEI"]);
	$query = "SELECT LATITUDE, LONGITUDE, TIME FROM coordinates WHERE IMEI = $imei";
	$result = $conn->query($query);

	if (!$result) {
	  die('Invalid query: ' . mysql_error());
	}
	header("Content-type: text/xml");
	// Iterate through the rows, adding XML nodes for each

	while ($row = $result->fetch_assoc()){
	  // ADD TO XML DOCUMENT NODE
	  $node = $dom->createElement("marker");
	  $newnode = $parnode->appendChild($node);
	  $newnode->setAttribute("lat",$row['LATITUDE']);
	  $newnode->setAttribute("long", $row['LONGITUDE']);
	  $newnode->setAttribute("time", $row['TIME']);
	}

	echo $dom->saveXML();

?>
