<?php
	session_start();
	
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zalogowany</title>
	
	<link rel="stylesheet" href="/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
</head>

<body>
	
	<div class="header">
		Strona główna
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
			<?php
				echo "Pracownik:  ".$_SESSION['name']." ".$_SESSION['surname'];
				echo "<br />";
				echo "<br />";
				echo "Zakończone dyżury:";
				echo "<br />";
				echo "I tu będzie 5 ostatnich dyżurów, w których ten pracownik brał udział";
				echo "<br />";
				echo "<br />";
				echo "Nadchodzące dyżury:";
				echo "<br />";
				echo "I tu będzie 5 nadchodzących dyżurów, w których ten pracownik będzie brał udział";
			?>

		</div>	
	
		<div style="clear:both"></div>
		
	</div>
</body>


</html>