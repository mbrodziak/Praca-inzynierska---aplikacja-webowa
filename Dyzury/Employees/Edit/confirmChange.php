<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	$ready = true;
	
	require_once "/xampp/htdocs/Dyzury/connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
		
	try
	{
		$connection = new mysqli($host, $db_user, $db_password, $db_name);
		$connection -> query ('SET NAMES utf8');
		$connection -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		if ($connection->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{
			$login = $_SESSION['login'];
			$result = $connection->query("SELECT * FROM pracownicy where login = '$login'");
			
			if (!$result) throw new Exception($connection->error);
				
			$row = $result->fetch_assoc();
			
			if(isset($_POST['password']))
			{
				$password = $_POST['password'];
				
				if(!password_verify($password, $row['haslo']))
				{
					$ready = false;
					$_SESSION['e_password'] = "Błędne hasło!";
				}
				else
				{
					if ($ready == true)
					{							
						if(isset($_SESSION['changeBirthday'])) 
						{
							$new_birthday = $_SESSION['changeBirthday'];
							if ($connection->query("UPDATE pracownicy set data_urodzenia = '$new_birthday' where login = '$login'"))
							{
								$_SESSION['succesChanged'] = true;
								$_SESSION['birthdayChanged'] = true;
								unset($_SESSION['changeBirthday']);
								header('Location: /Dyzury/Employees/Edit/changedData.php');
							}
							else
							{
								throw new Exception($connection->error);
							}
						}
						
						if(isset($_SESSION['changePhone']))
						{
							$new_phone = $_SESSION['changePhone'];
							if ($connection->query("UPDATE pracownicy set numer_telefonu = '$new_phone' where login = '$login'"))
							{
								$_SESSION['succesChanged'] = true;
								$_SESSION['phoneChanged'] = true;
								unset($_SESSION['changePhone']);
								header('Location: /Dyzury/Employees/Edit/changedData.php');
							}
							else
							{
								throw new Exception($connection->error);
							}
						}
					}
				}	
			}			
			$connection->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera!</span>';
		echo '<br />Informacja developerska: '.$e;
	}				
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zatwierdź zmianę</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

	
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
	
			<form method="post" novalidate>
				<div class="change">
					
					<input type="password" name="password" id="password" placeholder="Hasło" /> <br /> 
			
					<?php
						if (isset($_SESSION['e_password']))
						{
							echo '<div class="error">'.$_SESSION['e_password'].'</div>';
							unset($_SESSION['e_password']);
					}?> 
			
					<input type="submit" id="confirm" value="ZATWIERDŹ" />	
				</div>
			</form>
			
			<div class="cancel"><a href="<?php
			if(isset($_SESSION['changeBirthday']))
			{
				echo "/Dyzury/Employees/Edit/changeBirthday.php";
				unset ($_SESSION['changeBirthday']);
			}
			if(isset($_SESSION['changePhone']))
			{
				echo "/Dyzury/Employees/Edit/changePhone.php";
				unset($_SESSION['changePhone']);
			}
			?>"><input type="submit" class="cancel" value="ANULUJ" /></a></div>
		</div>
	</div>
</body>
</html>