<?php
	
	session_start();
	
	if(!isset($_SESSION['succesChanged']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['succesChanged']);
	}
	
	if (isset($_SESSION['rem_new_birthday'])) unset ($_SESSION['rem_new_birthday']);
	if (isset($_SESSION['rem_new_phone'])) unset ($_SESSION['rem_new_phone']);
	
	if (isset($_SESSION['e_new_birthday'])) unset ($_SESSION['e_new_birthday']);
	if (isset($_SESSION['e_new_phone'])) unset ($_SESSION['e_new_phone']);
	if (isset($_SESSION['e_old_pass'])) unset ($_SESSION['e_old_pass']);
	if (isset($_SESSION['e_new_pass'])) unset ($_SESSION['e_new_pass']);
	if (isset($_SESSION['e_repeat_pass'])) unset ($_SESSION['e_repeat_pass']);
?>




<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zmieniono</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />	
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
			
			<?php
				if($_SESSION['admin'] == 1)
				{
					echo '<a href="newShift.php" class="choose_option">
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
						</a>';	
				}			
			?>
			
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
				<?php
					if(isset($_SESSION['phoneChanged']))
					{
						echo '<h2>Zmieniono numer telefonu!</h2>';
						unset ($_SESSION['phoneChanged']);					
					}
					
					if(isset($_SESSION['birthdayChanged']))
					{
						echo '<h2>Zmieniono date urodzenia!</h2>';
						unset ($_SESSION['birthdayChanged']);					
					}
				
					if(isset($_SESSION['passChanged']))
					{
						echo '<h2>Zmieniono hasło!</h2>';
						unset ($_SESSION['passChanged']);					
					}
				
				
				?>
				
		</div>
	</div>
	
	
</body>
</html>