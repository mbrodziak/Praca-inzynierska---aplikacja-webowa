<?php
	
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
		
	$next_week_date = date("Y-m-d", strtotime("+1 week"));
		
	require_once __DIR__ . "/../../connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try
	{
		$connection = new mysqli($host, $db_user, $db_password, $db_name);
		$connection -> query ('SET NAMES utf8');
		$connection -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		if($connection->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno);
		}
		else
		{
			parse_str($_SERVER['QUERY_STRING'], $qs);
			$id = mysqli_real_escape_string($connection, $qs['shift_id']);
			
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
			FROM dyzury where id_dyzuru = '$id' limit 1";
		

			
			$result = $connection->query($query);
			
			if (!$result) throw new Exception($connection->error);
			
			$shift = $result->fetch_assoc();
			$can_edit = $shift['data_dyzuru'] >= $next_week_date;
			
			
			if (isset($_POST['shift_date']))
			{
				$ready = true;
				$shift_name = $_POST['shift_name'];
				$shift_date = $_POST['shift_date'];
				$shift_start = $_POST['shift_start'];
				$shift_length = $_POST['shift_length'];
				$shift_capacity = $_POST['shift_capacity'];	
				
				
				if($shift_name == NULL)
				{
					$shift_name = "Bez tytułu";
				}
				
				if(strlen($shift_name) > 30)
				{
					$ready = false;
					$_SESSION['e_shift_name'] = "Za długi tytuł!";
				}
				 
				
				$_SESSION['rem_shift_name'] = $shift_name;
				$_SESSION['rem_shift_date'] = $shift_date;
				$_SESSION['rem_shift_start'] = $shift_start;
				$_SESSION['rem_shift_length'] = $shift_length;
				$_SESSION['rem_shift_capacity'] = $shift_capacity;
				
				if($ready == true)
				{
					if($connection->query("update dyzury set tytul_dyzuru = '$shift_name', data_dyzuru = '$shift_date', godzina_rozpoczecia = '$shift_start', dlugosc_dyzuru = '$shift_length', ilosc_miejsc = '$shift_capacity' where id_dyzuru='$id'"))
					{
						$_SESSION['succes_shift_edit'] = true;
						header('Location: /Shifts/Edit/editedShift.php');
					}
					else
					{
						throw new Exception($connection->errno);
					}
				}
			}		
		}				
		$connection->close();		
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie dyżuru w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}				
	
?>

<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" >
	<title>Edytuj dyżur</title>

	
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
					<div>Edytuj dyżur</div>
				</h3>
				
				<?php 
					echo $can_edit ? "" : "<div class='alert alert-danger' role='alert'>
						Nie można edytować danych!
					</div>";			
				?>
	
				<form method="post">
				  <div class="form-group">
					<label>Nazwa dyżuru</label>
					<input type="text" class="form-control" name="shift_name" id="shift_name" placeholder="Nazwa dyżuru" value="<?php echo $shift['tytul_dyzuru'] ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Data dyżuru</label>
					<input type="date" class="form-control" name="shift_date" id="shift_date" placeholder="Data dyżuru" min="<?php echo $next_week_date ?>" 
					value="<?php echo $shift['data_dyzuru'] ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Godzina rozpoczęcia</label>
					<input type="time" class="form-control" name="shift_start" id="shift_start" placeholder="Godzina rozpoczęcia" 
					value="<?php echo $shift['godzina_rozpoczecia'] ?>"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Długość dyżuru</label>
					<input type="number" class="form-control" name="shift_length" id="shift_length" placeholder="Długość dyżuru" min="1" step="0.5" value="<?php echo $shift['dlugosc_dyzuru'] ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Ilość miejsc</label>
					<input type="number" class="form-control" name="shift_capacity" id="shift_capacity" placeholder="Ilość miejsc" min="<?php echo $shift['zajete'] > 2 ? $shift['zajete'] : 2 ?>" value="<?php echo $shift['ilosc_miejsc'] ?>"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
						
				  <button type="submit" class="btn btn-primary"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>>Zatwierdź</button>
				</form>

			</div>
		</div>
	</div>
	
</body>	
</html>