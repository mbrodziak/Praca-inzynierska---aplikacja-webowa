<?php
	
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
								
	require_once __DIR__ . "/../../connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try
	{
		$connection = new mysqli($host, $db_user, $db_password, $db_name);
		$connection -> query ('SET NAMES utf8');
		$connection -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		if($connection->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno);
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
					$_SESSION['e_password'] = "Błędne hasło!";
				}
				else
				{								
					if (isset($_SESSION['ready']))
					{
						$shift_name = $_SESSION['shift_name'];
						$shift_date = $_SESSION['shift_date'];
						$shift_start = $_SESSION['shift_start'];
						$shift_length = $_SESSION['shift_length'];
						$capacity = $_SESSION['capacity'];
						
						if($connection->query("INSERT INTO dyzury values (NULL, '$shift_name', '$shift_date', '$shift_start', '$shift_length', '$capacity')"))
						{
							$_SESSION['succes_shift'] = true;
							header('Location: /Shifts/New/addedShift.php');		
						}
						else
						{
							throw new Exception($connection->errno);
						}
						
					}
				}
			}
			$connection->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie dyżuru w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}				
?>





<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Dodaj nowy dyżur</title>
	
	<link rel="stylesheet" href="/Style/style.css" type="text/css" />
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
	
			<form method="post">
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
			<div class="cancel"><a href="/Shifts/New/newShift.php"><input type="submit" class="cancel" value="ANULUJ" /></a></div>
				</div>	
			</form>
		</div>

</body>


</html>