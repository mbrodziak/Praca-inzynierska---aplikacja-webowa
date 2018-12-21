<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../../connect.php";
	
	$query = "SELECT  
		dyzury.id_dyzuru, 
        (
        	SELECT  dyzury_pracownikow.id_pracownika, dyzury_pracownikow.zarejestrowanie,
        	FROM    dyzury_pracownikow d
        	WHERE   d.id_dyzuru = dyzury.id_dyzuru and d.potwierdzone = 0
        ) as zajete
	FROM dyzury";
	
	$query2 = "select dyzury.id_dyzuru, pracownicy.imie, pracownicy.nazwisko, dyzury_pracownikow.zarejestrowanie from 
	dyzury inner join pracownicy inner join dyzury_pracownikow where potwierdzone = 0"; 
	
	mysqli_report(MYSQLI_REPORT_STRICT);
		

	$employee_on_shift = [];
	
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
			$result = $connection->query("select id, id_dyzuru, id_pracownika, zarejestrowanie from dyzury_pracownikow where potwierdzone = 0");
			//$result = $connection->query($query2);
			if (!$result) throw new Exception($connection->error);
			
			$result2 = $connection->query("select imie, nazwisko from pracownicy inner join dyzury_pracownikow where pracownicy.id_pracownika = dyzury_pracownikow.id_pracownika and dyzury_pracownikow.potwierdzone = 0");
			
			if (!$result2) throw new Exception($connection->error);
			
			$result3 = $connection->query("select dyzury_pracownikow.id, dyzury_pracownikow.id_dyzuru, dyzury_pracownikow.zarejestrowanie, pracownicy.imie, pracownicy.nazwisko from pracownicy inner join dyzury_pracownikow where pracownicy.id_pracownika = dyzury_pracownikow.id_pracownika and dyzury_pracownikow.potwierdzone = 0");
			
			if (!$result3) throw new Exception($connection->error);
			
			
			$num_rows = $result->num_rows;
			$num_rows2 = $result2->num_rows;
			$num_rows3 = $result3->num_rows;
			
			for($i = 1; $i <= $num_rows3; $i++)
			{				
				$row3 = $result3->fetch_assoc();
				$employee_on_shift[] = $row3;
				// $id_row[$i] = $row['id'];
				// $id_dyzuru[$i] = $row['id_dyzuru'];
				// $id_pracownika[$i] = $row['id_pracownika'];
				// $zarejestrowanie[$i] = $row['zarejestrowanie'];
				
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
	<title>Zalogowany</title>

	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	
</head>

<body>

	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	  <a class="navbar-brand" href="/">NA61 HW Shift</a>
	 
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Przełącznik nawigacji">
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
			<?php 
			if($_SESSION['admin'] == 1)
			{
				echo "<li class='nav-item'>
					<a class='nav-link active' href='/Shifts/Register/applicationAdmin.php'>Zgłoszenia</a>
				</li>";
			}
			else echo "<li class='nav-item'>
					<a class='nav-link' href='/Shifts/Register/applicationNoAdmin.php'>Zgłoszenia</a>
				</li>";
			?>
		</ul>
		
		<ul class="navbar-nav">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?php echo $_SESSION['name']." ".$_SESSION['surname']; ?>
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
				<h3 class="d-flex flex-row justify-content-between my-3">
					<div>Lista zgłoszeń</div>
				</h3>
				<table class="table table-hover">
				  <thead>
					<tr align='center'>
					  <th scope="col">#</th>
					  <th scope="col">Identyfikator dyżuru</th>
					  <th scope="col">Imie i nazwisko</th>
					  <th scope="col">Zgłoszenie</th>
					  <th scope="col">Akcje</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($employee_on_shift as $employee_shift)
						{
							if($employee_shift['zarejestrowanie'] == 1) $employee_shift['zarejestrowanie'] = "Zarejestrowanie";
							else $employee_shift['zarejestrowanie'] = "Wyrejestrowanie";
							
							echo " 
							<tr align='center'>
							  <th scope='row'>" . $employee_shift['id'] . "</th>
							  <td>" . $employee_shift['id_dyzuru'] . "</td>
							  <td>" . $employee_shift['imie'] ." ". $employee_shift['nazwisko'] .  "</td>
							  <td>" . $employee_shift['zarejestrowanie'] . "</td>
							  <td>";
									echo "<a href='/Shifts/Register/confirmRegisterOnShift.php?id=" . $employee_shift['id'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='left' title='Potwierdź'>
										<img src='/Assets/Icons/add.svg' />
									</a>";
				
									// echo " <a href='/Shifts/Deregister/confirmDeregisterOnShift.php?id=" . $employee_shift['id'] . "' class='btn btn-secondary btn-sm' 
									// data-toggle='tooltip' data-placement='left' title='Odrzuć'>
										// <img src='/Assets/Icons/remove.svg' />
									// </a>
								
								echo "</td>
							</tr>";
						}
					?>
				  </tbody>
				</table>
			</div>
		</div>
	</div>
		
</body>
</html>