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
			$q_sort_order = mysqli_real_escape_string($connection, (isset($qs['sort_order']) ? $qs['sort_order'] : "ASC"));
			$q_sort_by = mysqli_real_escape_string($connection, (isset($qs['sort_by']) ? $qs['sort_by'] : "id_pracownika"));
			//"name" jest aliasem na: "imie {order}, nazwisko {order}"
			$sort_order = $q_sort_order;
			$sort_by = ($q_sort_by === "name" ? "imie " . $sort_order . ", nazwisko" : $q_sort_by);
								
			$login = $_SESSION['login'];
			
			$query = "SELECT * 
			FROM pracownicy 
			WHERE login != '" . $login . "'
			ORDER BY " . $sort_by . " " . $sort_order;
			
			//echo $query;
			$result = $connection->query($query);
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
			<li class="nav-item active">
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
					<div>Lista pracowników</div>
					<?php 
					if($_SESSION['admin'] == 1)
					{
						echo "<a href='/Employees/New/newEmployee.php' class='btn btn-primary'>
							DODAJ PRACOWNIKA <img src='/Assets/Icons/add.svg' />
						</a>";					
					}
					?>
				</h3>
				<table class="table table-hover">
				  <thead>
					<tr align="center">
						<th scope="col">
							<a href="/Employees/cadre.php?sort_by=id_pracownika&sort_order=<?php echo $q_sort_order === "ASC" ? "DESC" : "ASC" ?>">
								#
								<?php 
								if($q_sort_by === "id_pracownika" && $q_sort_order === "ASC")
								{
									echo "<img src='/Assets/Icons/up.svg' />";
								}
								
								if($q_sort_by === "id_pracownika" && $q_sort_order === "DESC")
								{
									echo "<img src='/Assets/Icons/down.svg' />";
								}
								?>
							</a>
						</th>
						<th scope="col">
							<a href="/Employees/cadre.php?sort_by=name&sort_order=<?php echo $q_sort_order === "ASC" ? "DESC" : "ASC" ?>">
								Imie i nazwisko
								<?php 
								if($q_sort_by === "name" && $q_sort_order === "ASC")
								{
									echo "<img src='/Assets/Icons/up.svg' />";
								}
								
								if($q_sort_by === "name" && $q_sort_order === "DESC")
								{
									echo "<img src='/Assets/Icons/down.svg' />";
								}
								?>
							</a>
						</th>
						<th scope="col">
							<a href="/Employees/cadre.php?sort_by=adres_email&sort_order=<?php echo $q_sort_order === "ASC" ? "DESC" : "ASC" ?>">
								Adres e-mail
								<?php 
								if($q_sort_by === "adres_email" && $q_sort_order === "ASC")
								{
									echo "<img src='/Assets/Icons/up.svg' />";
								}
								
								if($q_sort_by === "adres_email" && $q_sort_order === "DESC")
								{
									echo "<img src='/Assets/Icons/down.svg' />";
								}
								?>
							</a>
						</th>
						<th scope="col">
							<a href="/Employees/cadre.php?sort_by=admin&sort_order=<?php echo $q_sort_order === "ASC" ? "DESC" : "ASC" ?>">
								Admin
								<?php 
								if($q_sort_by === "admin" && $q_sort_order === "ASC")
								{
									echo "<img src='/Assets/Icons/up.svg' />";
								}
								
								if($q_sort_by === "admin" && $q_sort_order === "DESC")
								{
									echo "<img src='/Assets/Icons/down.svg' />";
								}
								?>
							</a>
						</th>
						<?php 
						if($_SESSION['admin'] == 1)
						{
							echo "<th scope='col'>Akcje</th>";
						}
						?>
						
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
							  <td>" . $employee['adres_email'] . "</td>
							  <td>" . $employee['admin'] . "</td>
							  <td>
							  	<a href='/Employees/detailsEmployee.php?employee_id=" . $employee['id_pracownika'] . "' class='btn btn-secondary btn-sm' 
								data-toggle='tooltip' data-placement='left' title='Szczegóły dyżuru'>
									<img src='/Assets/Icons/search.svg' />
								</a> ";
								
							if($_SESSION['admin'] == 1)
							{
									if ($employee['admin'] == "Nie")  
									echo "<a href='/Employees/Permissions/givePermission.php?employee_id=" . $employee['id_pracownika'] . "' 
									class='btn btn-secondary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Nadaj uprawnienia'>
										<img src='/Assets/Icons/add.svg' />
									</a>";									
									else echo " <a href='/Employees/Permissions/receivePermission.php?employee_id=" . $employee['id_pracownika'] . "' 
									class='btn btn-secondary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Odbierz uprawnienia'>
										<img src='/Assets/Icons/remove.svg' />
									</a>";
									echo "  <a href='/Employees/Edit/resetPass.php?employee_id=" . $employee['id_pracownika'] . "' class='btn btn-secondary btn-sm' 
									data-toggle='tooltip' data-placement='bottom' title='Resetuj hasło'>
										<img src='/Assets/Icons/edit.svg' />
									</a>";
									echo " <a href='/Employees/deleteEmployee.php?employee_id=" . $employee['id_pracownika'] . "' class='btn btn-danger btn-sm'
									data-toggle='tooltip' data-placement='bottom' title='Usuń pracownika'>
										<img src='/Assets/Icons/delete.svg' />
									</a>
								</td>";
							}
						echo "</tr>";
						}
					?>
					
				  </tbody>
				</table>
			</div>
		</div>
	</div>	


</body>


</html>