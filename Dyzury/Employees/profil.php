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
			$login = $_SESSION['login'];
			$result = $connection->query("SELECT * FROM pracownicy WHERE login = '$login'");
				
			if (!$result) throw new Exception($connection->error);
			
			$row = $result->fetch_assoc();
			
			$name = $row['imie'];
			$surname = $row['nazwisko'];
			// $_SESSION['birthday'] = $row['data_urodzenia'];
			// $_SESSION['email'] = $row['adres_email'];
			// $_SESSION['phone'] = $row['numer_telefonu'];			
			$birthday = $row['data_urodzenia'];
			$email = $row['adres_email'];
			$phone = $row['numer_telefonu'];
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
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
	<title>Profil</title>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="/">NA61 HW Shift</a>
	  
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" >
			<span class="navbar-toggler-icon"></span>
		</button>

	  <div class="collapse navbar-collapse" id="mainmenu">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/">Strona główna</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/Shifts/shift.php">Zarządzaj dyżurami</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/Employees/cadre.php">Zarządzaj pracownikami</a>
			</li>
			
		</ul>
		
		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
					<?php echo $_SESSION['name']." ".$_SESSION['surname']; ?>
				</a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="/Employees/profil.php">Profil</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/logout.php">Wyloguj się</a>
				</div>
			</li>
		</ul>
	  </div>
	</nav>
	
	<div class="container">
		<h3 class="d-flex flex-row justify-content-between my-3">
			<div>Profil pracownika</div>
		</h3>
		
		<div class="row my-4">	
			<div class="col">
				<img src="/Assets/Images/profil.jpg" alt="profil" class="img-thumbnail">
			</div>
			
			<div class="col">
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
					echo "<br />";
					echo "Hasło: ";
				?>
			</div>
			
			<div class="col">
				<?php
					echo $name;
					echo "<br />";
					echo $surname;
					echo "<br />";
					echo $birthday;
					echo "
						<a href='/Employees/Edit/changeBirthday.php' class='btn btn-sm'>
							EDYTUJ
						</a>";
					echo "<br />";
					echo $email;
					echo "<br />";
					echo $phone;
					echo "
						<a href='/Employees/Edit/changePhone.php' class='btn btn-sm' >
							EDYTUJ 
						</a>";
					echo "<br />";
					echo $_SESSION['login'];
					echo "<br />";
					echo $_SESSION['pass'];	
					echo "
						<a href='/Employees/Edit/changePass.php' class='btn btn-sm'>
							EDYTUJ
						</a>";					
				?>
			</div>
		</div>
	</div>
</body>
</html>