<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	if(isset($_POST['new_phone']))
	{
		$new_phone = $_POST['new_phone'];
		$_SESSION['rem_new_phone'] = $new_phone;
			
		if($new_phone == $_SESSION['phone'])
		{
			header('Location: /Employees/Edit/changePhone.php');
		}
		else
		{
			$_SESSION['changePhone'] = $new_phone;
			header('Location: /Employees/Edit/confirmChange.php');
		}
	}
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Edytuj numer telefonu</title>
	
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
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
	
			<form method="post" novalidate>
				<div class="change">
			
					<input type="tel" name="new_phone" id="new_phone" placeholder="Numer telefonu (opcjonalnie)" pattern="[0-9]{9}" value="<?php
					if (isset($_SESSION['rem_new_phone']))
					{
						echo $_SESSION['rem_new_phone'];
						unset($_SESSION['rem_new_phone']);
					}
					else echo $_SESSION['phone'];?>"/> 

					<?php
						if (isset($_SESSION['e_new_phone']))
						{
							echo '<div class="error">'.$_SESSION['e_new_phone'].'</div>';
							unset($_SESSION['e_new_phone']);
					}?> 
			
					<input type="submit" id="further" value="DALEJ" />	
				</div>			
			</form>
			<div class="cancel"><a href="/Employees/Edit/changeDataChoice.php"><input type="submit" id="cancel" value="ANULUJ" /></a></div>
		</div>
	</div>
</body>
</html>