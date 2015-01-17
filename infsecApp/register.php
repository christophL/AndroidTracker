<?PHP

$uname = "";
$imei = "";
$pw = "";
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
	$pw = $_POST['password'];
	$imei = $_POST['imei'];

	$uname = htmlspecialchars($uname);
	$pw = htmlspecialchars($pw);
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

	//compute hash of password, and store it instead of plaintext pw
	$pw = sha1($pw);

	//escape characters
	$uname = quote_smart($uname, $conn);
	$pw = quote_smart($pw, $conn);
	$imei = quote_smart($imei, $conn);

	//check whether IMEI is already registered
	$query = "SELECT * FROM users WHERE IMEI = $imei";
	$result = $conn->query($query);
	if($result->num_rows != 0) {
		$conn->close();
		exit("Error: IMEI already exists!");
	}

	//check whether username already exists
	$query = "SELECT * FROM users WHERE USERNAME = $uname";
	$result = $conn->query($query);
	if($result->num_rows != 0) {
		$conn->close();
		exit("Error: username already exists!");
	}

	$query = "INSERT INTO users ( IMEI, USERNAME, PASSWORD) VALUES ($imei, $uname, $pw)";

	if ($conn->query($query) === TRUE) {
		$conn->close();
		session_start();
		$_SESSION['authenticated'] = 'yes';
		header("Location: map.php?IMEI=$imei");
	} else {
		$conn->close();
		//TODO: proper error management
		//echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>


<html>
<head>
<title>Register new user</title>
</head>
<body>
Register your IMEI:
<FORM NAME ="form1" METHOD ="POST" ACTION ="register.php">
<br>

Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100">
<br>
Password: <INPUT TYPE = 'PASSWORD' Name ='password'  value="<?PHP print $pw;?>" maxlength="100">
<br>
IMEI: <INPUT TYPE = 'TEXT' Name ='imei'  value="<?PHP print $imei;?>" maxlength="15">

<P align = left>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Register">
</P>

</FORM>

<P>
<?PHP print $errorMessage;?>

</body>
</html>
