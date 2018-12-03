<?php
	session_start();
	
	if (isset($_SESSION['signed']) && $_SESSION['signed'] == true)
	{
		header('Location: signed.php');
		exit();
	}
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Panel logowania</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

</head>
<body>

	<div id="loginPanel">
		PANEL LOGOWANIA
	</div>
	<form action="signIn.php" method="post">
		<div id="loginField">
			<input type="text" name="login" id="login" placeholder="Login"/> 
			<input type="password" name="pass" id="pass" placeholder="Hasło" /> 
			<input type="submit" id="signIn" value="ZALOGUJ SIĘ" />			
		</div>
	</form>
	
<?php
	if(isset($_SESSION['error'])){
		echo "<center>".$_SESSION['error']."</center>";
		unset ($_SESSION['error']);
	}
?> 

</body>
</html>