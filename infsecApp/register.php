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

function generateSHA512Salt() {
	//16 char salt, but first three are given
	$length = 13;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./';
    $charactersLength = strlen($characters);
    $randomString = '$6$';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
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

	//compute hash of password, and store it instead of plaintext pw
	$salt = generateSHA512Salt();
	$pw = crypt($pw,$salt);

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
		//echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>


