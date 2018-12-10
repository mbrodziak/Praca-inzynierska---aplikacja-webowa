<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
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
			$result = $connection->query("select * from pracownicy");
			if (!$result) throw new Exception($connection->error);
			$num_rows = $result->num_rows;
			
			for($i = 1; $i <= $num_rows; $i++)
			{
				//$result = $connection->query("select * from pracownicy where id_pracownika = '$i'"); 
			
				//if (!$result) throw new Exception($connection->error);
				
				$row = $result->fetch_assoc();
				
				$lp[$i] = $row['id_pracownika']; 
				$name[$i] = $row['imie'];
				$surname[$i] = $row['nazwisko'];
				$birthday[$i] = $row['data_urodzenia'];
				$email[$i] = $row['adres_email'];
				$phone[$i] = $row['numer_telefonu'];
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
	
	
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zalogowany</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
</head>

<body>
	
	<div class="header">
		Kadra
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
			
			<div class="cadreNumber">
				<div class="cadre">L.p</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						echo $lp[$i]."<br />";
						echo "<br />";
					}
				?>
			</div>
			
			<div class="cadreNames">

				<div class="cadre">Imie i nazwisko</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						echo $name[$i]." ".$surname[$i]."<br />";
						echo "<br />";
					}
				?>
			</div>
			
			<div class="cadreBirthday">
				<div class="cadre">Data urodzenia</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						echo $birthday[$i]."<br />";
						echo "<br />";
					}
				?>
			</div> 
			
			<div class="cadreEmail">
				<div class="cadre">Adres e-mail</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						echo $email[$i]."<br />";
						echo "<br />";
					}
				?>
			</div>
			
			<div class="cadrePhone">
				<div class="cadre">Numer telefonu</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						echo $phone[$i]."<br />";
						echo "<br />";
					}
				?>
			</div>
			
			<div class="cadreAdmin">
				<div class="cadre">Admin</div>
				<?php
					echo "<br />";
					for($i = 1; $i <= $num_rows; $i++)
					{
						if($admin[$i] == 1)
						{
							$czyAdmin[$i] = "Tak";
						}
						else
						{
							$czyAdmin[$i] = "Nie";
						}
						echo $czyAdmin[$i]."<br />";
						echo "<br />";
					}
				?>
			</div>
			
			<div style="clear:both;"></div>

		</div>	
	
		<div style="clear:both"></div>
		
	</div>
</body>


</html>