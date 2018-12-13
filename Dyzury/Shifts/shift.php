<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../connect.php";
	
	$query = "SELECT  
		dyzury.id_dyzuru, 
		dyzury.tytul_dyzuru,
		dyzury.godzina_rozpoczecia,
		dyzury.data_dyzuru,
		dyzury.dlugosc_dyzuru,
		dyzury.ilosc_miejsc,
        (
        	SELECT  COUNT(*)
        	FROM    dyzury_pracownikow d
        	WHERE   d.id_dyzuru = dyzury.id_dyzuru
        ) as zajete
	FROM dyzury";
	
	mysqli_report(MYSQLI_REPORT_STRICT);
		

	$shifts = [];
	
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
			$result = $connection->query($query);
			if (!$result) throw new Exception($connection->error);
			
			$num_rows = $result->num_rows;
			
			for($i = 1; $i <= $num_rows; $i++)
			{				
				$row = $result->fetch_assoc();
				$shifts[] = $row;
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
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
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
			<li class="nav-item active">
				<a class="nav-link" href="/Shifts/shift.php">Zarządzaj dyżurami</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/Employees/cadre.php">Zarządzaj pracownikami</a>
			</li>
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
					<div>Lista dyżurów</div>
					<?php 
					if($_SESSION['admin'] == 1)
					{
						echo "<a href='/Shifts/New/newShift.php' class='btn btn-primary'>
							DODAJ DYŻUR <img src='/Assets/Icons/add.svg' />
						</a>";
					}
					?>
				</h3>
				<table class="table table-hover">
				  <thead>
					<tr align='center'>
					  <th scope="col">#</th>
					  <th scope="col">Tytuł</th>
					  <th scope="col">Rozpoczęcie</th>
					  <th scope="col">Długość (h)</th>
					  <th scope="col">Wolne miejsca</th>
					  <th scope="col">Akcje</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($shifts as $shift)
						{
							echo " 
							<tr align='center'>
							  <th scope='row'>" . $shift['id_dyzuru'] . "</th>
							  <td>" . $shift['tytul_dyzuru'] . "</td>
							  <td>" . $shift['data_dyzuru'] . " " . $shift['godzina_rozpoczecia'] . "</td>
							  <td>" . $shift['dlugosc_dyzuru'] . "</td>
							  <td>" . ($shift['ilosc_miejsc'] - $shift['zajete']) . "/" . $shift['ilosc_miejsc'] . "</td>
							  <td> 
							  	<a href='/Employees/detailsShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm'>
									<img src='/Assets/Icons/search.svg' />
								</a>";
								if($_SESSION['admin'] == 1)
								{
									echo "
									<a href='/Shifts/Edit/editShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm'>
										<img src='/Assets/Icons/edit.svg' />
									</a>
									<a href='/Shifts/deleteShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-danger btn-sm'>
										<img src='/Assets/Icons/delete.svg' />
									</a>";
								}
							  echo "</td>
							</tr>		
							";
						}
					?>
					
				  </tbody>
				</table>
			</div>
		</div>
	</div>
		
</body>
</html>