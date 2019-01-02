<?php
	session_start();
	
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	$confirmed_shift = [];
	$registered_shift = [];
	$today_date = date("Y-m-d");
	
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
			$id = $_SESSION['id_employee'];
			
			$query = "SELECT
				dyzury.id_dyzuru, 
				dyzury.tytul_dyzuru, 
				dyzury.data_dyzuru, 
				dyzury.godzina_rozpoczecia, 
				dyzury.data_zakonczenia  
			FROM dyzury 
			INNER JOIN dyzury_pracownikow 
			WHERE dyzury.id_dyzuru = dyzury_pracownikow.id_dyzuru 
			AND dyzury_pracownikow.id_pracownika = '$id' 
			AND potwierdzone = '1' 
			AND zarejestrowanie = '1' 
			ORDER BY data_dyzuru DESC";			
			
			$query2 = "SELECT 
				dyzury.id_dyzuru, 
				dyzury.tytul_dyzuru, 
				dyzury.data_dyzuru,
				dyzury.godzina_rozpoczecia, 
				dyzury.data_zakonczenia 
			FROM dyzury 
			INNER JOIN dyzury_pracownikow 
			WHERE dyzury.id_dyzuru = dyzury_pracownikow.id_dyzuru 
			AND dyzury_pracownikow.id_pracownika = '$id' 
			AND potwierdzone = '0' AND zarejestrowanie = '1' 
			ORDER BY data_dyzuru DESC";
			
			$result = $connection->query($query);
			if (!$result) throw new Exception($connection->error);			
			
			$result2 = $connection->query($query2);
			if (!$result2) throw new Exception($connection->error);
			
			$num_rows = $result->num_rows;
			$num_rows2 = $result2->num_rows;
			
			for($i = 1; $i <= $num_rows; $i++)
			{
				$row = $result->fetch_assoc();
				$confirmed_shift[] = $row;
			}			
			
			for($i = 1; $i <= $num_rows2; $i++)
			{
				$row2 = $result2->fetch_assoc();
				$registered_shift[] = $row2;
			}
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
	<title>Strona główna</title>
	
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
				<a class="nav-link active" href="/">Strona główna</a>
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
	
	<div class="container my-3">
		<div class="row">
			<div class="col">
				<h3 class="d-flex flex-row justify-content-between my-3">
					<div>Confirmed shifts</div>
				</h3>
				
				<table class="table table-hover">	
				  <thead>
					<tr align="center">
						<th scope="col" >#</th>
						<th scope="col">Tytuł</th>
						<th scope="col">Rozpoczęcie</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($confirmed_shift as $shift)
						{
							if($today_date <= $shift['data_zakonczenia'])
							{
								echo " 
								<tr align='center'>
								  <th scope='row'>" . $shift['id_dyzuru'] . "</th>
								  <td>" . $shift['tytul_dyzuru'] . "</td>
								  <td>" . $shift['data_dyzuru'] . " " . $shift['godzina_rozpoczecia'] . "</td>
								</tr>";
							}
						}
					?>
				  </tbody>
				</table>
						
				<h3 class="d-flex flex-row justify-content-between my-3">
					<div>Ended shifts</div>
				</h3>
				
				<table class="table table-hover">	
				  <thead>
					<tr align="center">
						<th scope="col" >#</th>
						<th scope="col">Tytuł</th>
						<th scope="col">Rozpoczęcie</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($confirmed_shift as $shift)
						{
							if($today_date >= $shift['data_zakonczenia'])
							{
								echo " 
								<tr align='center'>
								  <th scope='row'>" . $shift['id_dyzuru'] . "</th>
								  <td>" . $shift['tytul_dyzuru'] . "</td>
								  <td>" . $shift['data_dyzuru'] . " " . $shift['godzina_rozpoczecia'] . "</td>
								</tr>";
							}
						}
					?>
				  </tbody>
				</table>
				  
				<?php
					if($_SESSION['admin'] == 0)
					{ 
					  echo 
						"<h3 class='d-flex flex-row justify-content-between my-3'>
							<div>Registered shifts</div>
						</h3>
						
						<table class='table table-hover'>
						  <thead>
							<tr align='center'>
								<th scope='col' >#</th>
								<th scope='col'>Tytuł</th>
								<th scope='col'>Data dyzuru</th>
							</tr>
						  </thead>
						  <tbody>";
							
								foreach ($registered_shift as $shift2)
								{
									echo " 
									<tr align='center'>
									  <th scope='row'>" . $shift2['id_dyzuru'] . "</th>
									  <td>" . $shift2['tytul_dyzuru'] . "</td>
									  <td>" . $shift2['data_dyzuru'] . " " . $shift2['godzina_rozpoczecia'] . "</td>
									</tr>";
								}
						echo "</tbody>
						</table>";
					}
				?>
			</div>
		</div>
	</div>
		
</body>
</html>