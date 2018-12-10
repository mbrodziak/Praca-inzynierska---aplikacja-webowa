<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../connect.php";
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
			$loginn = $_SESSION['login'];
			$result = $connection->query("select * from pracownicy where admin = 1 and login != '$loginn'");
			
			if (!$result) throw new Exception($connection->error);
			
			$num_rows = $result->num_rows;
			
			for($i = 1; $i <= $num_rows; $i++)
			{	
				$row = $result->fetch_assoc();
				
				$lp[$i] = $row['id_pracownika']; 
				$name[$i] = $row['imie'];
				$surname[$i] = $row['nazwisko'];
				$birthday[$i] = $row['data_urodzenia'];
				$email[$i] = $row['adres_email'];
				$phone[$i] = $row['numer_telefonu'];
				$login[$i] = $row['login'];
				$admin[$i] = $row['admin'];				
			}
		}
		$connection->close();
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie praocownika w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
	
		
	if(isset($_POST['employeesA']))
	{
		$employeesA = $_POST['employeesA'];
		for($i = 0; $i < count($employeesA); $i++)
		{
			$_SESSION['empA'][$i] = $employeesA[$i];
			header('Location: /Employees/Permissions/confirmPermission.php');
		}
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
		ODBIERZ UPRAWNIENIA
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
				<div class="names">
					<?php
						echo "<br />";
						for($i = 1; $i <= $num_rows; $i++)
						{
							echo '<label><input type="checkbox" name="employeesA[]" value="';
							echo $login[$i];
							echo '">';
							echo "  ".$name[$i]." ".$surname[$i]."<br />";
							echo '</label>';
							echo "<br />";
							$_SESSION['receivePermission'] = true;
						}
					?>
				</div>
				
				<input type="submit" id="receive" value="ODBIERZ" />
			</form>
		</div>
		
	
		<div style="clear:both"></div>
		
	</div>
</body>


</html>