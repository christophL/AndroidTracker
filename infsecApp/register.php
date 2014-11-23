<?PHP

$uname = "";
$imei = "";
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
	$uname = $_POST['username'];
	$imei = $_POST['imei'];

	$uname = htmlspecialchars($uname);
	$imei = htmlspecialchars($imei);

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
	$uname = quote_smart($uname, $conn);
	$imei = quote_smart($imei, $conn);

	$query = "INSERT INTO users ( IMEI, USERNAME) VALUES ($imei, $uname)";

	if ($conn->query($query) === TRUE) {
		$conn->close();
		header("Location: map.php?IMEI=$imei");
	} else {
		$conn->close();
		echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>


<html>
<head>
<title>Register new user</title>
</head>
<body>
Register a username for your IMEI:
<FORM NAME ="form1" METHOD ="POST" ACTION ="register.php">

Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100">
IMEI: <INPUT TYPE = 'TEXT' Name ='imei'  value="<?PHP print $imei;?>" maxlength="15">

<P align = center>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Register">
</P>

</FORM>

<P>
<?PHP print $errorMessage;?>




</body>
</html>
