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
			
			$num_rows = $result->num_rows;
			
			for($i = 1; $i <= $num_rows; $i++)
			{				
				$row = $result->fetch_assoc();
				$employee_on_shift[] = $row;
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
	  <a class="navbar-brand" href="/">Nazwa aplikacji</a>
	 
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
			<li class="nav-item">
				<a class="nav-link active" href="#">Potwierdzenia</a>
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
					  <th scope="col">Id dyzuru</th>
					  <th scope="col">Id pracownika</th>
					  <th scope="col">Zarejestrowane</th>
					  <th scope="col">Potwierdź</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($employee_on_shift as $employee_shift)
						//for($i = 1; $i <= $num_rows; $i++)
						{
							echo " 
							<tr align='center'>
							  <th scope='row'>" . $employee_shift['id'] . "</th>
							  <td>" . $employee_shift['id_dyzuru'] . "</td>
							  <td>" . $employee_shift['id_pracownika'] . "</td>
							  <td>" . $employee_shift['zarejestrowanie'] . "</td>
							  <td>";
								if($employee_shift['zarejestrowanie'] == 1)
								{ 
									echo " <a href='/Shifts/Register/confirmRegisterOnShift.php?id=" . $employee_shift['id'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='left' title='Zarejestruj się na dyżur'>
										<img src='/Assets/Icons/group_add.svg' />
									</a>";
								}
								else
								{
									echo "<a href='/Shifts/Deregister/confirmDeregisterOnShift.php?id=" . $employee_shift['id'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='left' title='Wyrejestruj się z dyżuru'>
										<img src='/Assets/Icons/person.svg' />
									</a>";
								}
								echo "
								</td>
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