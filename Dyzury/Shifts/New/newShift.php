<?php
	
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	$next_week_date = date("Y-m-d", strtotime("+1 week"));
							
	if (isset($_POST['shift_date']))
	{
		$ready = true;
		$shift_name = $_POST['shift_name'];
		$shift_date = $_POST['shift_date'];
		$shift_start = $_POST['shift_start'];
		$shift_date_end = $_POST['shift_date_end'];
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
		 
		 
		if($shift_length == NULL)
		{
			$shift_length = 1;
		} 
		 
		if($shift_capacity == NULL)
		{
			$shift_capacity = 2;
		}
		
		$_SESSION['rem_shift_name'] = $shift_name;
		$_SESSION['rem_shift_date'] = $shift_date;
		$_SESSION['rem_shift_start'] = $shift_start;
		$_SESSION['rem_shift_date_end'] = $shift_date_end;
		$_SESSION['rem_shift_length'] = $shift_length;
		$_SESSION['rem_shift_capacity'] = $shift_capacity;
		
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
							if($connection->query("INSERT INTO dyzury values (NULL, '$shift_name', '$shift_date', '$shift_start', '$shift_date_end', 
							'$shift_length', '$shift_capacity')"))
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
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie dyżuru w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}				
	}
?>





<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
	<title>Dodaj nowy dyżur</title>
	
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
					<div>Dodaj dyżur</div>
				</h3>
	
				<form method="post">
				  <div class="form-group">
				  
					<label>Nazwa dyżuru</label>
					<input type="text" class="form-control" name="shift_name" id="shift_name" placeholder="Nazwa dyżuru" value="<?php
					if (isset($_SESSION['rem_shift_name']))
					{
						echo $_SESSION['rem_shift_name'];
						unset($_SESSION['rem_shift_name']);
					}?>" />
					
					<?php
						if (isset($_SESSION['e_shift_name']))
						{
							echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_shift_name'] .	"</div>";
							unset ($_SESSION['e_shift_name']);
						}
					?> 
					
				  </div>
				  
				  <div class="form-group">
					<label>Data dyżuru</label>
					<input type="date" class="form-control" name="shift_date" id="shift_date" placeholder="Data dyżuru" required min="<?php echo $next_week_date ?>" 
					value="<?php
					if (isset($_SESSION['rem_shift_date']))
					{
						echo $_SESSION['rem_shift_date'];
						unset($_SESSION['rem_shift_date']);
					}?>"/>
				  </div>
				  
				  <?php
					if (isset($_SESSION['e_shift_date']))
					{
						echo '<div class="error">'.$_SESSION['e_shift_date'].'</div>';
						unset ($_SESSION['e_shift_date']);
					}
					?> 
				  
				  <div class="form-group">
					<label>Godzina rozpoczęcia</label>
					<input type="time" class="form-control" name="shift_start" id="shift_start" placeholder="Godzina rozpoczęcia" required value="<?php
					if (isset($_SESSION['rem_shift_start']))
					{
						echo $_SESSION['rem_shift_start'];
						unset($_SESSION['rem_shift_start']);
					}?>" />
				  </div>
				  
				  <div class="form-group">
					<label>Data zakończenia dyżuru</label>
					<input type="date" class="form-control" name="shift_date_end" id="shift_date_end" placeholder="Data zakończenia" required min="<?php echo $next_week_date ?>" 
					value="<?php
					if (isset($_SESSION['rem_shift_date_end']))
					{
						echo $_SESSION['rem_shift_date_end'];
						unset($_SESSION['rem_shift_date_end']);
					}?>"/>
				  </div>
				  
				  <div class="form-group">
					<label>Długość dyżuru(dniówki)</label>
					<input type="number" class="form-control" name="shift_length" id="shift_length" placeholder="Długość dyżuru (domyślnie 1h)" min="1" step="0.5" value="<?php
					if (isset($_SESSION['rem_shift_length']))
					{
						echo $_SESSION['rem_shift_length'];
						unset($_SESSION['rem_shift_length']);
					}?>" />
				  </div>
				  
				  <div class="form-group">
					<label>Ilość miejsc</label>
					<input type="number" class="form-control" name="shift_capacity" id="shift_capacity" placeholder="Ilość miejsc (domyślnie 2)" min="2" value="<?php
					if (isset($_SESSION['rem_shift_capacity']))
					{
						echo $_SESSION['rem_shift_capacity'];
						unset($_SESSION['rem_shift_capacity']);
					}?>"/> 
				  </div>
				  
				  <div class="form-group">
					<label>Potwierdź dodanie dyżuru</label>
					<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" />	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] .	"</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
				  
				  <div>
					<button type="submit" class="btn btn-primary">DODAJ DYŻUR</button>
					<a href="/Shifts/shift.php" class="btn btn-primary">ANULUJ</a>
				 </div>
				</form>
			</div>
		</div>
	</div>
	
</body>	
</html>