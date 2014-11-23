<?PHP

$uname = "";
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
	$uname = htmlspecialchars($uname);

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

	$query = "SELECT IMEI FROM users WHERE USERNAME = $uname";
	$result = $conn->query($query);
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$imei = $row["IMEI"];
		$conn->close();
		header("Location: map.php?IMEI=$imei");
	} else {
		//error, more than one IMEI per username
		$conn->close();
		echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>


<html>
<head>
<title>Show your phone activity</title>
</head>
<body>
Type in your username to see what your phone has been up to:
<FORM NAME ="form1" METHOD ="POST" ACTION ="start.php">

Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100">

<P align = center>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Show">
</P>

</FORM>

<P>
<?PHP print $errorMessage;?>

</body>
</html>