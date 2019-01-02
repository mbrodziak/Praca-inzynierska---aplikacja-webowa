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
			parse_str($_SERVER['QUERY_STRING'], $qs);
			$id = mysqli_real_escape_string($connection, $qs['shift_id']);
			
			$query = "SELECT * FROM pracownicy INNER JOIN dyzury_pracownikow WHERE pracownicy.id_pracownika = dyzury_pracownikow.id_pracownika 
			AND dyzury_pracownikow.id_dyzuru = '$id'";
			
			$result = $connection->query($query);
			if (!$result) throw new Exception($connection->error);
			$num_rows = $result->num_rows;
		
			for($i = 1; $i <= $num_rows; $i++)
			{				
				$row = $result->fetch_assoc();
				$employees[] = $row;						
			}
			
			$result2 = $connection->query("SELECT * FROM dyzury WHERE id_dyzuru = '$id'");
			if (!$result2) throw new Exception($connection->error);
			
			$row2 = $result2->fetch_assoc();
			
			$shift_id = $row2['id_dyzuru'];
			$shift_name = $row2['tytul_dyzuru'];
			$shift_date = $row2['data_dyzuru'];
			$shift_start = $row2['godzina_rozpoczecia'];
			$shift_length = $row2['dlugosc_dyzuru'];
			$shift_capacity = $row2['ilosc_miejsc'];
			
			$result3 = $connection->query("SELECT * FROM dyzury_pracownikow WHERE id_dyzuru = '$shift_id' 
			AND (potwierdzone = 1 OR (potwierdzone = 0 AND zarejestrowanie = 0))");
			if (!$result3) throw new Exception($connection->error);
			
			$shift_busy = $result3->num_rows;
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
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" >
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
					<div>Lista pracowników</div>
				</h3>
				<?php
					echo "Id dyżuru: " . $shift_id;
					echo "<br />";
					echo "Nazwa dyżuru: " . $shift_name;
					echo "<br />";
					echo "Data dyżuru: " . $shift_date;
					echo "<br />";
					echo "Godzina rozpoczęcia: " . $shift_start;
					echo "<br />";
					echo "Długość dyżuru: " . $shift_length;
					echo "<br />";
					echo "Wolne miejsca: " . ($shift_capacity - $shift_busy) . "/" . $shift_capacity;
					echo "<br />";
					echo "<br />";
				?>
				<table class="table table-hover">
				  <thead>
					<tr align="center">
						<th scope="col" >#</th>
						<th scope="col">Imie i nazwisko</th>
						<th scope="col">Zgłoszenie</th>
						<th scope="col">Potwierdzone</th>
						<th scope="col">Admin</th>
						<th scope="col">Akcje</th>
					</tr>
				  </thead>
				  <tbody>
					<?php
						foreach ($employees as $employee)
						{
							if($employee['admin'] == 1) $employee['admin'] = "Tak";
							else $employee['admin'] = "Nie";
							
							if($employee['zarejestrowanie'] == 1) $employee['zarejestrowanie'] = "Zarejestrowanie";
							else $employee['zarejestrowanie'] = "Wyrejestrowanie";
							
							if($employee['potwierdzone'] == 1) $employee['potwierdzone'] = "Tak";
							else $employee['potwierdzone'] = "Nie";
							
							echo " 
							<tr align='center'>
							  <th scope='row'>" . $employee['id_pracownika'] . "</th>
							  <td>" . $employee['imie'] . " " . $employee['nazwisko'] . "</td>
							  <td>" . $employee['zarejestrowanie'] . " </td>
							  <td>" . $employee['potwierdzone'] . " </td>
							  <td>" . $employee['admin'] . " </td>
							  <td>";
								if(($employee['potwierdzone'] == "Tak") && ($_SESSION['id_employee'] == $employee['id_pracownika']))
								{
									echo "<a href='/Shifts/Deregister/deregisterOnShift.php?shift_id=" . $shift_id . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Wyrejestruj się z dyżuru'>
										<img src='/Assets/Icons/remove.svg' />
									</a>";
								}							
								if(($employee['potwierdzone'] == "Nie") && ($_SESSION['id_employee'] == $employee['id_pracownika']))
								{
									echo "<a href='/Shifts/withdrawApplication.php?id=" . $employee['id'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='left' title='Wycofaj zgłoszenie'>
										<img src='/Assets/Icons/remove.svg' />
									</a>";
								}
								if($_SESSION['admin'])
								{
									if(($employee['potwierdzone'] == "Nie") && ($employee['zarejestrowanie'] == "Zarejestrowanie"))
									{
										echo "<a href='/Shifts/Register/confirmRegisterOnShift.php?id=" . $employee['id'] . "&id_employee=". $employee['id_pracownika']. "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Potwierdź'>
											<img src='/Assets/Icons/confirm.svg' />
										</a>";
										echo " <a href='/Shifts/Register/discardRegisterOnShift.php?id=" . $employee['id'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Odrzuć'>
											<img src='/Assets/Icons/discard.svg' />
									 </a> ";
									}
									if(($employee['potwierdzone'] == "Nie") && ($employee['zarejestrowanie'] == "Wyrejestrowanie"))
									{
										echo "<a href='/Shifts/Deregister/confirmDeregisterOnShift.php?id=" . $employee['id'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Potwierdź'>
											<img src='/Assets/Icons/confirm.svg' />
										</a>";
										echo " <a href='/Shifts/Deregister/discardDeregisterOnShift.php?id=" . $employee['id'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Odrzuć'>
											<img src='/Assets/Icons/discard.svg' />
									 </a> ";
									}
									if(($employee['potwierdzone'] == "Tak") && ($employee['zarejestrowanie'] == "Zarejestrowanie") 
										&& ($_SESSION['id_employee'] != $employee['id_pracownika']))
									{
										echo " <a href='/Shifts/Register/deleteEmployeeWithShift.php?id=" . $employee['id'] . "' class='btn btn-secondary btn-sm' 
										data-toggle='tooltip' data-placement='left' title='Usuń'>
											<img src='/Assets/Icons/DELETE.svg' />
									 </a> ";
									}
									
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