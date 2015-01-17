<?PHP

$uname = "";
$pw = "";
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
	$pw = $_POST['password'];

	$uname = htmlspecialchars($uname);
	$pw = htmlspecialchars($pw);

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

	//compute hash of pw to check with the one saved in DB
	$pw = sha1($pw);
	$pw = quote_smart($pw, $conn);

	//escape characters
	$uname = quote_smart($uname, $conn);

	$query = "SELECT IMEI FROM users WHERE USERNAME = $uname AND PASSWORD = $pw";
	$result = $conn->query($query);
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$imei = $row["IMEI"];
		$conn->close();
		session_start();
		$_SESSION['authenticated'] = 'yes';
		header("Location: map.php?IMEI=$imei");
	} else {
		//username or pw wrong
		$conn->close();
		header("Location: login.php");
		//echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>

<html>
<head>
<title>Show your phone activity</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("button").click(function(){
    $("#register").slideToggle();
  });
});
</script>
</head>
<body>
Login to see what your phone has been up to:
<br>
<br>

<FORM NAME ="form1" METHOD ="POST" ACTION ="index.php">

Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100">
<br>
Password: <INPUT TYPE = 'PASSWORD' Name ='password'  value="<?PHP print $pw;?>" maxlength="100">
<br>
<P align = left>
<INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Login">
</P>

</FORM>

<button>Or register here!</button>
<br>
<div id="register" style="display:none">
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
</div>

<P>
<?PHP print $errorMessage;?>

</body>
</html>
