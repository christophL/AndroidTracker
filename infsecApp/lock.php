<?PHP

$newPw = "";
$newPwRepeat = "";
$errorMessage = "";
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
	$newPw = $_POST['password'];
	$newPwRepeat = $_POST['password_repeat'];
	$imei = $_POST['imei'];

	$newPw = htmlspecialchars($newPw);
	$newPwRepeat = htmlspecialchars($newPwRepeat);

	//==========================================
	//	CONNECT TO THE LOCAL DATABASE
	//==========================================
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
	//escape characters
	$newPw = quote_smart($newPw, $conn);
	$newPwRepeat = quote_smart($newPwRepeat, $conn);

	if($newPw != $newPwRepeat) {
		die("Passwords are not identical!");
	}

	$query = "UPDATE users SET TO_LOCK=1, NEW_PHONE_PW=$newPw WHERE IMEI=$imei";
	$result = $conn->query($query);
	if($result == true) {
		$conn->close();
	} else {
		$conn->close();
	}
	header("Location: map.php?IMEI=$imei");
}
?>
