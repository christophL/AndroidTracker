<?PHP

$imei = "";

//==========================================
//	ESCAPE DANGEROUS SQL CHARACTERS
//==========================================
function quote_smart($value, $handle) {
	//echo $value;
   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }

   if (!is_numeric($value)) {
       $value = "'" . mysqli_real_escape_string($handle, $value) . "'";
   }
	//echo $value;
   return $value;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$imei = $_POST['imei'];

	$newPw = htmlspecialchars($imei);

	//==========================================
	//	CONNECT TO THE LOCAL DATABASE
	//==========================================
	$servername = "localhost";
	$username = "infsecApp";
	$password = "changeMe";
	$dbname = "infsecApp";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
 	   die("Connection failed: " . $conn->connect_error);
	} 
	//escape characters
	$newPw = quote_smart($imei, $imei);

	$query = "UPDATE users SET WIPE=1 WHERE IMEI=$imei";
	$result = $conn->query($query);
	if($result == true) {
		$conn->close();
	} else {
		$conn->close();
	}
	header("Location: map.php?IMEI=$imei");
}
?>
