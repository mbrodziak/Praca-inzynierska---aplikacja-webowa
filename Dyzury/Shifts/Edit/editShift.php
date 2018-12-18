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
					$loginn = $_SESSION['login'];
					$result = $connection->query("SELECT haslo FROM pracownicy where login = '$loginn'");
					
					if (!$result) throw new Exception($connection->error);
						
					$row = $result->fetch_assoc();
					$password = $_POST['confirm_pass'];
				
					if(!empty($password))
					{
						if(!password_verify($password, $row['haslo'])) $_SESSION['e_password'] = "Błędne hasło!";
						
						else
						{
							if($connection->query("update dyzury set tytul_dyzuru = '$shift_name', data_dyzuru = '$shift_date', 
							godzina_rozpoczecia = '$shift_start', dlugosc_dyzuru = '$shift_length', ilosc_miejsc = '$shift_capacity' where id_dyzuru='$id'"))
							{
								header('Location: /Shifts/shift.php');
							}
							else
							{
								throw new Exception($connection->errno);
							}
						}
					}
					else $_SESSION['e_password'] = "Proszę potwierdzić hasłem!";
				}
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
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" >
	<title>Edytuj dyżur</title>

	
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
					<input type="text" class="form-control" name="shift_name" id="shift_name" placeholder="Nazwa dyżuru" value="<?php 
					if(isset($_SESSION['rem_shift_name']))
					{
						echo $_SESSION['rem_shift_name'];
						unset($_SESSION['rem_shift_name']);
					}
					else echo $shift['tytul_dyzuru']; ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Data dyżuru</label>
					<input type="date" class="form-control" name="shift_date" id="shift_date" placeholder="Data dyżuru" min="<?php echo $next_week_date ?>" 
					value="<?php
					if (isset($_SESSION['rem_shift_date']))
					{
						echo $_SESSION['rem_shift_date'];
						unset($_SESSION['rem_shift_date']);	
					}
					else echo $shift['data_dyzuru'] ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Godzina rozpoczęcia</label>
					<input type="time" class="form-control" name="shift_start" id="shift_start" placeholder="Godzina rozpoczęcia" 
					value="<?php 					
					if (isset($_SESSION['rem_shift_start']))
					{
						echo $_SESSION['rem_shift_start'];
						unset($_SESSION['rem_shift_start']);
					}
					else echo $shift['godzina_rozpoczecia'] ?>"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Długość dyżuru</label>
					<input type="number" class="form-control" name="shift_length" id="shift_length" placeholder="Długość dyżuru" min="1" step="0.5" value="<?php 
					if (isset($_SESSION['rem_shift_length']))
					{
						echo $_SESSION['rem_shift_length'];
						unset($_SESSION['rem_shift_length']);
					}
					else echo $shift['dlugosc_dyzuru'] ?>" 
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				  <div class="form-group">
					<label>Ilość miejsc</label>
					<input type="number" class="form-control" name="shift_capacity" id="shift_capacity" placeholder="Ilość miejsc" 
					min="<?php echo $shift['zajete'] > 2 ? $shift['zajete'] : 2 ?>" value="<?php
					if (isset($_SESSION['rem_shift_capacity']))
					{
						echo $_SESSION['rem_shift_capacity'];
						unset($_SESSION['rem_shift_capacity']);
					}					
					else echo $shift['ilosc_miejsc'] ?>"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>
				  </div>
				  
				   <div class="form-group">
					<label>Potwierdź edycje dyżuru</label>
					<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" 					
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>/>	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] .	"</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
						
				 <div>
				  <button type="submit" class="btn btn-primary"
					<?php 
						echo !$can_edit ? "disabled" : "";
					?>>ZATWIERDŹ</button>
					
					<a href="/Shifts/shift.php" class="btn btn-primary">ANULUJ</a>
				</div>
					
				</form>
			</div>			
		</div>
	</div>
</body>	
</html>