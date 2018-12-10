<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Edytuj dane</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script>
		
		
	
	</script>
	
</head>

<body>
	
	<div class="header">
		Edytuj dane
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>

			<a href="/Dyzury/signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="/Dyzury/Employees/profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="/Dyzury/Shifts/shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>
			
			<?php
				if($_SESSION['admin'] == 1)
				{
					echo '<a href="/Dyzury/Shifts/New/newShift.php" class="choose_option">
							<div class="option">
								Dodaj dyżur
							</div>
						</a>
						
						<a href="/Dyzury/Employees/New/newEmployee.php" class="choose_option">
							<div class="option">
								Dodaj pracownika
							</div class="option">
						</a>
						
						<a href="/Dyzury/Employees/Permissions/givePermission.php" class="choose_option">
							<div class="option">
								Nadaj uprawnienia
							</div class="option">
						</a>
						
						<a href="/Dyzury/Employees/Permissions/receivePermission.php" class="choose_option">
							<div class="option">
								Odbierz uprawnienia
							</div class="option">
						</a>';	
				}			
			?>
						
			<a href="/Dyzury/Employees/cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="/Dyzury/logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
		
		<div class="no_name_yet">
			<a href="/Dyzury/Employees/Edit/changeBirthday.php"><div class="options">Edytuj datę urodzenia</div></a>
			<a href="/Dyzury/Employees/Edit/changePhone.php"><div class="options">Edytuj numer telefonu</div></a>
			<a href="/Dyzury/Employees/Edit/changePass.php"><div class="options">Zmień hasło</div></a>
			
		</div>
	</div>
</body>
</html>