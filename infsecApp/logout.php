<?php
	session_start();
	if($_SESSION['authenticated'] == 'yes') {
		session_unset();
		session_destroy();
		header("Location: login.php");
	}
?>
