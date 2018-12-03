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
					$ready = false;
					$_SESSION['e_password'] = "Błędne hasło!";
				}
				else
				{	 
					if(isset($_SESSION['givePermission']))
					{
						//echo "give";
						for($i = 0; $i < count($_SESSION['empNA']); $i++)
						{
							//echo "givefor";
							$employeesNA[$i] = $_SESSION['empNA'][$i];
							$connection->query("UPDATE pracownicy set admin = '1' where login = '$employeesNA[$i]'");

							if ($connection->connect_errno != 0) throw new Exception(mysqli_connect_errno());
						
						}
						//echo "givezafor";
						unset($_SESSION['givePermission']);
						unset($_SESSION['empNA']);
						header('Location: givedPermission.php');
					}
					
					if(isset($_SESSION['receivePermission']))
					{
						//echo "receive";
						for($i = 0; $i < count($_SESSION['empA']); $i++)
						{
							//echo "receivefor";
							$employeesA[$i] = $_SESSION['empA'][$i];
							$connection->query("UPDATE pracownicy set admin = '0' where login = '$employeesA[$i]'");
							
							if ($connection->connect_errno != 0) throw new Exception(mysqli_connect_errno());
						}
						//echo "receivezafor";
						unset($_SESSION['receivePermission']);
						unset($_SESSION['empA']);
						header('Location: receivedPermission.php');
					}					
				}
			}	
			$connection->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! </span>';
		echo '<br />Informacja developerska: '.$e;
	}
?>	

<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zatwierdź zmianę</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

	
</head>

<body>
	
	<div class="header">
		Uprawnienia
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
	</div>
</body>
</html>	
