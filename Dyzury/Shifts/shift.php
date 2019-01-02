<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	require_once __DIR__ . "/../connect.php";
	
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
			parse_str($_SERVER['QUERY_STRING'], $qs);
			$sort_order = mysqli_real_escape_string($connection, (isset($qs['sort_order']) ? $qs['sort_order'] : "ASC"));
			$sort_by = mysqli_real_escape_string($connection, (isset($qs['sort_by']) ? $qs['sort_by'] : "id_dyzuru"));
	
			$query = "SELECT
				dyzury.id_dyzuru, 
				dyzury.tytul_dyzuru,
				dyzury.godzina_rozpoczecia,
				dyzury.data_dyzuru,
				dyzury.dlugosc_dyzuru,
				dyzury.data_zakonczenia,
				dyzury.ilosc_miejsc,
				(
					SELECT  COUNT(*)
					FROM    dyzury_pracownikow d
					WHERE   (
						d.id_dyzuru = dyzury.id_dyzuru AND (d.potwierdzone = 1 OR 
						(d.zarejestrowanie = 0 AND d.potwierdzone = 0))
					)
				) AS zajete,
				(
					SELECT COUNT(d.id_pracownika)
					FROM dyzury_pracownikow d
					WHERE d.id_dyzuru = dyzury.id_dyzuru AND d.id_pracownika = " . $_SESSION['id_employee'] . "
				) AS jest_zarejestrowany  
			FROM dyzury
			ORDER BY " . $sort_by . " " . $sort_order;
	
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
	 
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu">
			<span class="navbar-toggler-icon"></span>
		</button>

	  <div class="collapse navbar-collapse" id="mainmenu">
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
					  <th scope="col">
						<a id="linktable" href="/Shifts/shift.php?sort_by=id_dyzuru&sort_order=<?php echo $sort_order === "ASC" ? "DESC" : "ASC" ?>">
							#
							<?php 
							if($sort_by === "id_dyzuru" && $sort_order === "ASC")
							{
								echo "<img src='/Assets/Icons/up.svg' />";
							}
							
							if($sort_by === "id_dyzuru" && $sort_order === "DESC")
							{
								echo "<img src='/Assets/Icons/down.svg' />";
							}
							?>
						</a>					  
					  </th>
					  <th scope="col">
						<a id="linktable" href="/Shifts/shift.php?sort_by=tytul_dyzuru&sort_order=<?php echo $sort_order === "ASC" ? "DESC" : "ASC" ?>">
							Tytuł
							<?php 
							if($sort_by === "tytul_dyzuru" && $sort_order === "ASC")
							{
								echo "<img src='/Assets/Icons/up.svg' />";
							}
							
							if($sort_by === "tytul_dyzuru" && $sort_order === "DESC")
							{
								echo "<img src='/Assets/Icons/down.svg' />";
							}
							?>
						</a>
					  </th>
					  <th scope="col">
						<a id="linktable" href="/Shifts/shift.php?sort_by=data_dyzuru&sort_order=<?php echo $sort_order === "ASC" ? "DESC" : "ASC" ?>">
							Rozpoczęcie
							<?php 
							if($sort_by === "data_dyzuru" && $sort_order === "ASC")
							{
								echo "<img src='/Assets/Icons/up.svg' />";
							}
							
							if($sort_by === "data_dyzuru" && $sort_order === "DESC")
							{
								echo "<img src='/Assets/Icons/down.svg' />";
							}
							?>
						</a>
					  </th>
					  <th scope="col">
					   	<a id="linktable" href="/Shifts/shift.php?sort_by=data_zakonczenia&sort_order=<?php echo $sort_order === "ASC" ? "DESC" : "ASC" ?>">
							Data zakończenia
							<?php 
							if($sort_by === "data_zakonczenia" && $sort_order === "ASC")
							{
								echo "<img src='/Assets/Icons/up.svg' />";
							}
							
							if($sort_by === "data_zakonczenia" && $sort_order === "DESC")
							{
								echo "<img src='/Assets/Icons/down.svg' />";
							}
							?>
						</a>
					  </th>
					  <th scope="col">
						<a id="linktable" href="/Shifts/shift.php?sort_by=dlugosc_dyzuru&sort_order=<?php echo $sort_order === "ASC" ? "DESC" : "ASC" ?>">
							Długość (h)
							<?php 
							if($sort_by === "dlugosc_dyzuru" && $sort_order === "ASC")
							{
								echo "<img src='/Assets/Icons/up.svg' />";
							}
							
							if($sort_by === "dlugosc_dyzuru" && $sort_order === "DESC")
							{
								echo "<img src='/Assets/Icons/down.svg' />";
							}
							?>
						</a>
					  </th>
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
							  <td>" . $shift['data_zakonczenia'] . "</td>
							  <td>" . $shift['dlugosc_dyzuru'] . "</td>
							  <td>" . ($shift['ilosc_miejsc'] - $shift['zajete']) . "/" . $shift['ilosc_miejsc'] . "</td>
							  <td> 
							  	<a href='/Employees/detailsShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm' 
								data-toggle='tooltip' data-placement='left' title='Szczegóły dyżuru'>
									<img src='/Assets/Icons/search.svg' />
								</a> " .
								($shift['jest_zarejestrowany'] ?
									"" : 
									"<a href='/Shifts/Register/registerOnShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Zarejestruj się na dyżur'>
										<img src='/Assets/Icons/add.svg' />
									</a>") .
								(!$shift['jest_zarejestrowany'] ?
									"" : 
									"<a href='/Shifts/Deregister/deregisterOnShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Wyrejestruj się z dyżuru'>
										<img src='/Assets/Icons/remove.svg' />
									</a>");
																	
								if($_SESSION['admin'] == 1)
								{
									echo "
									<a href='/Shifts/Edit/editShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='bottom' title='Edytuj dyżur'>
										<img src='/Assets/Icons/edit.svg' />
									</a>
									<a href='/Shifts/deleteShift.php?shift_id=" . $shift['id_dyzuru'] . "' class='btn btn-danger btn-sm'
									data-toggle='tooltip' data-placement='bottom' title='Usuń dyżur'>
										<img src='/Assets/Icons/DELETE.svg' />
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