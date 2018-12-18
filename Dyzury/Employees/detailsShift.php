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
			
			$query = "select * from pracownicy inner join dyzury_pracownikow where pracownicy.id_pracownika = dyzury_pracownikow.id_pracownika 
			and dyzury_pracownikow.id_dyzuru = '$id' and (dyzury_pracownikow.potwierdzone = 1 
			OR (dyzury_pracownikow.potwierdzone = 0 and dyzury_pracownikow.zarejestrowanie = 0))";
			
			$result = $connection->query($query);
			if (!$result) throw new Exception($connection->error);
			$num_rows = $result->num_rows;
		
			for($i = 1; $i <= $num_rows; $i++)
			{				
				$row = $result->fetch_assoc();
				$employees[] = $row;						
			}
			
			$result2 = $connection->query("SELECT * FROM dyzury where id_dyzuru = '$id'");
			if (!$result2) throw new Exception($connection->error);
			
			$row2 = $result2->fetch_assoc();
			
			$id_shift = $row2['id_dyzuru'];
			$shift_name = $row2['tytul_dyzuru'];
			$shift_date = $row2['data_dyzuru'];
			$shift_start = $row2['godzina_rozpoczecia'];
			$shift_length = $row2['dlugosc_dyzuru'];
			$shift_capacity = $row2['ilosc_miejsc'];
			
			$result4 = $connection->query("select * from dyzury_pracownikow where id_dyzuru = '$id_shift' 
			and (potwierdzone = 1 or (potwierdzone = 0 and zarejestrowanie = 0))");
			if (!$result4) throw new Exception($connection->error);
			
			$shift_busy = $result4->num_rows;
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
	  <a class="navbar-brand" href="/">Nazwa aplikacji</a>
	  
	  	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Przełącznik nawigacji">
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
			<?php 
			if($_SESSION['admin'] == 1)
			{
				echo "<li class='nav-item'>
					<a class='nav-link' href='/Shifts/Register/applicationAdmin.php'>Zgłoszenia</a>
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
					<div>Lista pracowników</div>
				</h3>
				<?php
					echo "Id dyżuru: " . $id_shift;
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
				?>
				<table class="table table-hover">
				  <thead>
					<tr align="center">
						<th scope="col" >#</th>
						<th scope="col">Imie i nazwisko</th>
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
</body>
</html>