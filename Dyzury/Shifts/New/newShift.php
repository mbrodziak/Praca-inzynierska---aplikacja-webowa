<?php
	
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
							
	if (isset($_POST['shift_date']))
	{
		$ready = true;
		$shift_name = $_POST['shift_name'];
		$shift_date = $_POST['shift_date'];
		$shift_start = $_POST['shift_start'];
		$shift_length = $_POST['shift_length'];
		$capacity = $_POST['capacity'];	
		
		
		if($shift_name == NULL)
		{
			$shift_name = "Bez tytułu";
		}
		
		if(strlen($shift_name) > 30)
		{
			$ready = false;
			$_SESSION['e_shift_name'] = "Za długi tytuł!";
		}
		
		$today_date = date("Y-m-d");
		
		if($shift_date <= $today_date)
		{
			$ready = false;
			$_SESSION['e_shift_date'] = "Nie można dodać dyżuru w przeszłości oraz na dzień dzisiejszy!";
		}
			 
		if($capacity < 2 && !($capacity) == NULL)
		{
			$ready = false;
			$_SESSION['e_capacity'] = "Minimalna ilość miejsc w danym dyżurze to 2!";
		}		
		
		if($capacity == NULL)
		{
			$capacity = 2;
		}
		
		$_SESSION['rem_shift_name'] = $shift_name;
		$_SESSION['rem_shift_date'] = $shift_date;
		$_SESSION['rem_shift_start'] = $shift_start;
		$_SESSION['rem_shift_length'] = $shift_length;
		$_SESSION['rem_capacity'] = $capacity;
		
		require_once "/xampp/htdocs/dyzury/connect.php";
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
				if($ready == true)
				{
					$_SESSION['shift_name'] = $shift_name;
					$_SESSION['shift_date'] = $shift_date;
					$_SESSION['shift_start'] = $shift_start;
					$_SESSION['shift_length'] = $shift_length;
					$_SESSION['capacity'] = $capacity;
					$_SESSION['ready'] = true;
					
					header('Location: /Dyzury/Shifts/New/confirmShift.php');
				}
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie dyżuru w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}				
	}
?>





<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Dodaj nowy dyżur</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
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
	
			<form method="post">
				<div id="newShift">
					<input type="text" name="shift_name" id="shift_name" placeholder="Nazwa dyżuru"  value="<?php
					if (isset($_SESSION['rem_shift_name']))
					{
						echo $_SESSION['rem_shift_name'];
						unset($_SESSION['rem_shift_name']);
					}?>"/> 
			
					<?php
						if (isset($_SESSION['e_shift_name']))
						{
							echo '<div class="error">'.$_SESSION['e_shift_name'].'</div>';
							unset ($_SESSION['e_shift_name']);
						}
					?> 
			
					<input type="text" name="chooseDateShift" id="chooseDateShift" value="Wybierz datę dyżuru: " disabled/> 
			
					<input type="date" name="shift_date" id="shift_date" value="<?php
					if (isset($_SESSION['rem_shift_date']))
					{
						echo $_SESSION['rem_shift_date'];
						unset($_SESSION['rem_shift_date']);
					}?>"/>
			
					<?php
						if (isset($_SESSION['e_shift_date']))
						{
							echo '<div class="error">'.$_SESSION['e_shift_date'].'</div>';
							unset ($_SESSION['e_shift_date']);
						}
					?> 
			
					<input type="text" name="hourShiftStart" id="hourShiftStart" value="Godzina rozpoczęcia dyżuru: " disabled/> 
			
					<input type="time" name="shift_start" id="shift_start" value="<?php
					if (isset($_SESSION['rem_shift_start']))
					{
						echo $_SESSION['rem_shift_start'];
						unset($_SESSION['rem_shift_start']);
					}?>" /> 
			
					<input type="text" name="hourLengthShift" id="hourLengthShift" value="Długość dyżuru (w godzinach): " disabled/> 
			
					<input type="number" name="shift_length" id="shift_length"  placeholder="0" min=0 value="<?php
					if (isset($_SESSION['rem_shift_length']))
					{
						echo $_SESSION['rem_shift_length'];
						unset($_SESSION['rem_shift_length']);
					}?>" /> 
			
					<input type="text" name="places" id="places" value="Ilość miejsc: " disabled/> 
			
					<input type="number" name="capacity" id="capacity" placeholder="2" min=2 value="<?php
					if (isset($_SESSION['rem_capacity']))
					{
						echo $_SESSION['rem_capacity'];
						unset($_SESSION['rem_capacity']);
					}?>"/> 

					<?php
					if (isset($_SESSION['e_capacity']))
					{
						echo '<div class="error">'.$_SESSION['e_capacity'].'</div>';
						unset ($_SESSION['e_capacity']);
					}
					?> 
			
					<input type="submit" id="addShift" value="DODAJ DYŻUR" />	
				</div>
			</form>
		</div>

</body>


</html>