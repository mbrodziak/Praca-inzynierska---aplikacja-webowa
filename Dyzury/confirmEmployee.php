<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	require_once "connect.php";
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
					$_SESSION['e_password'] = "Błędne hasło!";
				}
				else
				{								
					if (isset($_SESSION['ready']))
					{
						$name = $_SESSION['emp_name'];
						$surname = $_SESSION['emp_surname'];
						$birthday = $_SESSION['emp_birthday'];
						$email= $_SESSION['emp_email'];
						$phone = $_SESSION['emp_phone'];
						$login = $_SESSION['emp_login'];
						$hash_pass = $_SESSION['emp_pass'];
						$admin2 = $_SESSION['emp_admin'];
						
						if ($connection->query("INSERT INTO pracownicy VALUES (NULL, '$name', '$surname', '$birthday', '$email', '$phone', '$login', '$hash_pass', 
						'$admin2', '0000-00-00 00:00:00')"))
						{
							$_SESSION['succes'] = true;
							header('Location: addedEmployee.php');
						}
						else
						{
							throw new Exception($connection->error);
						}
					}
				}
			}				
			$connection->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie praocownika w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
	//}
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Dodaj nowego pracownika</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script>
		
		
	
	</script>
	
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

			?>"><input type="submit" id="cancel" value="ANULUJ" /></a></div>
				</div>			
			</form>
		</div>
	</div>
</body>
</html>