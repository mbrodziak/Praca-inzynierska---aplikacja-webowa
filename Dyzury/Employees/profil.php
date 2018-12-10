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
			$login = $_SESSION['login'];
			$result = $connection->query("SELECT * FROM pracownicy where login = '$login'");
				
			if (!$result) throw new Exception($connection->error);
			
			$row = $result->fetch_assoc();
			
			$_SESSION['birthday'] = $row['data_urodzenia'];
			$_SESSION['email'] = $row['adres_email'];
			$_SESSION['phone'] = $row['numer_telefonu'];
		}
		$connection->close();
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
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zalogowany</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
</head>

<body>
	
	<div class="header">
		Profil
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
			<div class="img"><img src="/Dyzury/Style/Images/profil.jpg"></div>
		
			<div class="profil_info">
			
				<div class="description">
					<?php
						echo "Imie:  ";
						echo "<br />";
						echo "Nazwisko: ";
						echo "<br />";
						echo "Data urodzenia: ";
						echo "<br />";
						echo "Adres e-mail: ";
						echo "<br />";
						echo "Numer telefonu: ";;
						echo "<br />";
						echo "Login: ";
					?>
				</div>
				
				<div class="information">
					<?php
						echo $_SESSION['name'];
						echo "<br />";
						echo $_SESSION['surname'];
						echo "<br />";
						echo $_SESSION['birthday'];
						echo "<br />";
						echo $_SESSION['email'];
						echo "<br />";
						echo $_SESSION['phone'];
						echo "<br />";
						echo $_SESSION['login'];					
					?>
				</div>
				
				<div style="clear:both;"></div> 
				
				<div id="changeData"><a href="/Dyzury/Employees/Edit/changeDataChoice.php"><input type="submit" id="changeData" value="EDYTUJ DANE" /></a></div>
				
			</div>
			
			<div style="clear:both;"></div>
		
		</div>	
	
		<div style="clear:both"></div>
		
	</div>
</body>


</html>