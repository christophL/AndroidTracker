<?php

// Start XML file, create parent node

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a MySQL server

$connection=mysql_connect ('localhost', "root", "");
if (!$connection) {  die('Not connected : ' . mysql_error());}

// Set the active MySQL database

$db_selected = mysql_select_db("infsecApp", $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
}

// Select all the rows in the markers table with the given IMEI
$imei = htmlspecialchars($_GET["IMEI"]);
$query = "SELECT LATITUDE, LONGITUDE, TIME FROM coordinates WHERE IMEI = $imei";
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each

while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("lat",$row['LATITUDE']);
  $newnode->setAttribute("long", $row['LONGITUDE']);
  $newnode->setAttribute("time", $row['TIME']);
}

echo $dom->saveXML();

?>
