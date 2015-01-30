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
	
	//check whether imei consists of 15 digits!
	$pattern = '/^[0-9]{15}$/';
	if(!preg_match($pattern, $imei)) {
		die("Error: IMEI does not consist of 15 digits!\n");
	}

	//check whether password is empty
	if($pw == "") {
		die("Error: the chosen password is empty!");
	}

	//compute hash of password, and store it instead of plaintext pw
	$pw = password_hash($pw, PASSWORD_BCRYPT);

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

	$query = "INSERT INTO users ( IMEI, USERNAME, PASSWORD ) VALUES ($imei, $uname, $pw)";

	if ($conn->query($query) === TRUE) {
		$conn->close();
		session_start();
		$_SESSION['authenticated'] = 'yes';
		header("Location: map.php?IMEI=$imei");
	} else {
		$conn->close();
	}
}
?>


