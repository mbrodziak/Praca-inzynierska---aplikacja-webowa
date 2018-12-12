<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
		

	$employees = [];
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
				
				$row = $result->fetch_assoc();
				$employees[] = $row;
				
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
	
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
	
</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <a class="navbar-brand" href="/">Nazwa aplikacji</a>

	  <div class="collapse navbar-collapse" >
		<ul class="navbar-nav mr-auto">
			<li class="nav-item">
				<a class="nav-link" href="/">Strona główna</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/Shifts/shift.php">Zarządzaj dyżurami</a>
			</li>
			<li class="nav-item active">
				<a class="nav-link" href="/Employees/cadre.php">Zarządzaj pracownikami</a>
			</li>
		</ul>
		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Mateusz Brodziak
				</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="/Employees/profil.php">Profil</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="/logout.php">Wyloguj się</a>
				</div>
			</li>
		</ul>
	  </div>
	</nav>
	
	
	
	<div class="container">
		<div class="row">
			<div class="col">
				<h3 class="my-3">Lista pracowników</h3>
				<table class="table table-hover">
				  <thead>
					<tr align="center">
						<th scope="col" >#</th>
						<th scope="col">Imie i nazwisko</th>
						<th scope="col">Data urodzenia</th>
						<th scope="col">Adres e-mail</th>
						<th scope="col">Numer telefonu</th>
						<th scope="col">Login</th>
						<th scope="col">Admin</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($employees as $employee)
						{
							if($employee['admin'] == 1) $employee['admin'] = "Tak";
							else $employee['admin'] = "Nie";
							
							echo " 
							<tr align='center'>
							  <th scope='row'>" . $employee['id_pracownika'] . "</th>
							  <td>" . $employee['imie'] . " " . $employee['nazwisko'] . "</td>
							  <td>" . $employee['data_urodzenia'] . "</td>
							  <td>" . $employee['adres_email'] . "</td>
							  <td>" . $employee['login'] . "</td>
							  <td>" . $employee['login'] . "</td>
							  <td>" . $employee['admin'] . " </td>
							</tr>		
							";
						}
					?>
					
				  </tbody>
				</table>
			</div>
		</div>
	</div>	
	
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

</body>


</html>