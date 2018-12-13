<?php
	
	session_start();
	
	if(!isset($_SESSION['succes']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['succes']);
	}
	
	if (isset($_SESSION['rem_name'])) unset ($_SESSION['rem_name']);
	if (isset($_SESSION['rem_surname'])) unset ($_SESSION['rem_surname']);
	if (isset($_SESSION['rem_birthday'])) unset ($_SESSION['rem_birthday']);
	if (isset($_SESSION['rem_email'])) unset ($_SESSION['rem_email']);
	if (isset($_SESSION['rem_phone'])) unset ($_SESSION['rem_phone']);
	if (isset($_SESSION['rem_login'])) unset ($_SESSION['rem_login']);
	if (isset($_SESSION['rem_admin'])) unset ($_SESSION['rem_admin']);
	
	if (isset($_SESSION['e_name'])) unset ($_SESSION['e_name']);
	if (isset($_SESSION['e_surname'])) unset ($_SESSION['e_surname']);
	if (isset($_SESSION['e_login'])) unset ($_SESSION['e_login']);
	if (isset($_SESSION['e_pass'])) unset ($_SESSION['e_pass']);
	if (isset($_SESSION['e_email'])) unset ($_SESSION['e_email']);
	
	if (isset($_SESSION['emp_name'])) unset ($_SESSION['emp_name']);
	if (isset($_SESSION['emp_surname'])) unset ($_SESSION['emp_surname']);
	if (isset($_SESSION['emp_birthday'])) unset ($_SESSION['emp_birthday']);
	if (isset($_SESSION['emp_email'])) unset ($_SESSION['emp_email']);
	if (isset($_SESSION['emp_phone'])) unset ($_SESSION['emp_phone']);
	if (isset($_SESSION['emp_login'])) unset ($_SESSION['emp_login']);
	if (isset($_SESSION['emp_admin'])) unset ($_SESSION['emp_admin']);
	
	if (isset($_SESSION['ready'])) unset ($_SESSION['ready']);
	
	
	

	
	
?>




<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Dodano pracownika</title>
	
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
</head>


<body>
	
	<div class="header">
		DODAJ NOWEGO PRACOWNIKA 
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>

			<a href="/signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="/Employees/profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="/Shifts/shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>
			
			<?php
				if($_SESSION['admin'] == 1)
				{
					echo '<a href="/Shifts/New/newShift.php" class="choose_option">
							<div class="option">
								Dodaj dyżur
							</div>
						</a>
						
						<a href="/Employees/New/newEmployee.php" class="choose_option">
							<div class="option">
								Dodaj pracownika
							</div class="option">
						</a>
						
						<a href="/Employees/Permissions/givePermission.php" class="choose_option">
							<div class="option">
								Nadaj uprawnienia
							</div class="option">
						</a>
						
						<a href="/Employees/Permissions/receivePermission.php" class="choose_option">
							<div class="option">
								Odbierz uprawnienia
							</div class="option">
						</a>';	
				}			
			?>
						
			<a href="/Employees/cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="/logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
		
		<div class="no_name_yet">
				<h2>Dodano nowego pracownika!</h2>
		</div>
	</div>
	
	
</body>
</html>