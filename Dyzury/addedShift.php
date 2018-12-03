<?php
	
	session_start();
	
	if(!isset($_SESSION['succes_shift']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['succes_shift']);
	}
	
	if (isset($_SESSION['rem_shift_name'])) unset ($_SESSION['rem_shift_name']);
	if (isset($_SESSION['rem_shift_date'])) unset ($_SESSION['rem_shift_date']);
	if (isset($_SESSION['rem_shift_start'])) unset ($_SESSION['rem_shift_start']);
	if (isset($_SESSION['rem_shift_length'])) unset ($_SESSION['rem_shift_length']);
	if (isset($_SESSION['rem_capacity'])) unset ($_SESSION['rem_capacity']);
	
	if (isset($_SESSION['e_shift_name'])) unset ($_SESSION['e_shift_name']);
	if (isset($_SESSION['e_shift_date'])) unset ($_SESSION['e_shift_date']);
	if (isset($_SESSION['e_capacity'])) unset ($_SESSION['e_capacity']);
	
	if (isset($_SESSION['shift_name'])) unset ($_SESSION['shift_name']);
	if (isset($_SESSION['shift_date'])) unset ($_SESSION['shift_date']);
	if (isset($_SESSION['shift_start'])) unset ($_SESSION['shift_start']);
	if (isset($_SESSION['shift_length'])) unset ($_SESSION['shift_length']);
	if (isset($_SESSION['capacity'])) unset ($_SESSION['capacity']);
	
	if (isset($_SESSION['ready'])) unset ($_SESSION['ready']);
?>




<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Dodano dyżur</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />	
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
</head>


<body>
	
	<div class="header">
		DODAJ NOWY DYŻUR
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>

			<a href="signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>
			
			<a href="newShift.php" class="choose_option">
				<div class="option">
					Dodaj dyżur
				</div>
			</a>
			
			<a href="newEmployee.php" class="choose_option">
				<div class="option">
					Dodaj pracownika
				</div class="option">
			</a>
			
			<a href="noAdmin.php" class="choose_option">
				<div class="option">
					Nadaj uprawnienia
				</div class="option">
			</a>
			
			<a href="Admin.php" class="choose_option">
				<div class="option">
					Odbierz uprawnienia
				</div class="option">
			</a>
			
			<a href="cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
		
		<div class="no_name_yet">
				<h2>Dodano nowy dyżur!</h2>
		</div>
	</div>
	
</body>
</html>