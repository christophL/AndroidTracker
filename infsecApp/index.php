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

	//escape characters
	$uname = quote_smart($uname, $conn);

	$query = "SELECT IMEI, PASSWORD FROM users WHERE USERNAME = $uname";
	$result = $conn->query($query);
	if($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$imei = $row["IMEI"];
		$storedPw = $row["PASSWORD"];
		if(crypt($pw, $storedPw) != $storedPw) {
			die("Error: wrong password!\n");
		}
		$conn->close();
		session_start();
		$_SESSION['authenticated'] = 'yes';
		header("Location: map.php?IMEI=$imei");
	} else {
		//username or pw wrong
		$conn->close();
		header("Location: index.php");
		//echo "Error: " . $query . "<br>" . $conn->error;
	}
}
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>Show your phone activity</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("button").click(function(){
    $("#registerDiv").slideToggle();
  });
});
</script>
</head>
<body>
<div class="appName">
AndroidTracker
</div>
<div id="contentDiv">
<div id="loginDiv">
Login to see what your phone has been up to:
<br>
<br>

<FORM NAME ="form1" METHOD ="POST" ACTION ="index.php">
	<table>
		<tr>
			<td>Username:</td><td> <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100"></td>
		</tr>
		<tr>
			<td>Password:</td><td> <INPUT TYPE = 'PASSWORD' Name ='password'  value="<?PHP print $pw;?>" maxlength="100"></td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="2"><INPUT class="centerButton" TYPE = "Submit" Name = "Submit1"  VALUE = "Login"></td>
		</tr>
	</table>
</FORM>

<br>
<br>
<br>
<button class="registerButton">Or register here!</button>



<br>
<div id="registerDiv">
<FORM NAME ="form1" METHOD ="POST" ACTION ="register.php">
	<table>
		<tr>
			<td>Username:</td> <td><INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" maxlength="100"></td>
		</tr>
		<tr>
			<td>Password:</td> <td> <INPUT TYPE = 'PASSWORD' Name ='password'  value="<?PHP print $pw;?>" maxlength="100"> </td>
		</tr>
		<tr>
			<td>IMEI:</td><td><INPUT TYPE = 'TEXT' Name ='imei'  value="<?PHP print $imei;?>" maxlength="15"></td>
		</tr>
		<tr>
			<td colspan="2"><INPUT class="centerButton" TYPE = "Submit" Name = "Submit1"  VALUE = "Register"></td>
		</tr>
	</table>
</FORM>
</div>
</div>
</div>

<P>		
<?PHP print $errorMessage;?>

</body>
</html>
