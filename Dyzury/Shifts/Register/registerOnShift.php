<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	//$next_week_date = date("Y-m-d", strtotime("+1 week"));
	$tommorrow = date("Y-m-d", strtotime("+1 day"));
	$yesterday = date("Y-m-d", strtotime("-1 day"));
	$today = date("Y-m-d");
	//echo $tommorrow;
	require_once __DIR__ . "/../../connect.php";	
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
			$id = mysqli_real_escape_string($connection, $qs['shift_id']);
			$can_register = true;
			$can_register_date = true;
			$can_register_date2 = true;
			
			$employee_id = $_SESSION['id_employee'];
			$result = $connection->query("SELECT haslo, admin FROM pracownicy WHERE id_pracownika = '$employee_id'");
			
			if (!$result) throw new Exception($connection->error);	
			$row = $result->fetch_assoc();
			
			$result2 = $connection->query("SELECT data_dyzuru, data_zakonczenia, ilosc_miejsc FROM dyzury WHERE id_dyzuru = '$id'");
			if (!$result2) throw new Exception($connection->error);
			
			$row2 = $result2->fetch_assoc();
			$shift_date = $row2['data_dyzuru'];
			$shift_date_end = $row2['data_zakonczenia'];
			$shift_capacity = $row2['ilosc_miejsc'];
				
			$result3 = $connection->query("SELECT * FROM dyzury_pracownikow WHERE id_dyzuru = '$id' 
			AND (potwierdzone = 1 OR (potwierdzone = 0 AND zarejestrowanie = 0))");
			if (!$result3) throw new Exception($connection->error);
			
			$shift_busy = $result3->num_rows;
			
			$result4 = $connection->query("SELECT data_dyzuru, data_zakonczenia FROM dyzury INNER JOIN dyzury_pracownikow 
			WHERE dyzury.id_dyzuru = dyzury_pracownikow.id_dyzuru AND id_pracownika = '$employee_id'");
			if (!$result4) throw new Exception($connection->error);
			
			//$num_rows4 = $result4->num_rows;
			//var_dump($num_rows4);
			// for($i = 0; $i < $num_rows4; $i++)
			// {
				// $row4 = $result4->fetch_assoc();
				// $shift_date_emp[$i] = $row4['data_dyzuru'];
				// $shift_date_end_emp[$i] = $row4['data_zakonczenia'];								
			// }			
			
			// for($i = 0; $i < $num_rows4; $i++)
			// {
				// echo "R". " " . $shift_date_emp[$i] . "<br />";
				// echo "Z". " " . $shift_date_end_emp[$i]  . "<br />";
				// echo "DD". " " . $shift_date . "<br />";
				// echo "DZ". " " . $shift_date_end . "<br />";
				// if(($shift_date > $shift_date_emp[$i]) || ($shift_date <= $shift_date_emp[$i]))  && ($shift_date <= $shift_date_end_emp[$i])) 
				// {
					// $can_register_date2 = false;
				// }
			// }
			
			if($shift_date > $today)
			{ 
				if(($shift_capacity - $shift_busy) > "0")
				{
					if(isset($_POST['confirm_pass']))
					{
						$password = $_POST['confirm_pass'];
						if(!empty($password))
						{
							if(!password_verify($password, $row['haslo'])) $_SESSION['e_password'] = "Błędne hasło!";
							
							else
							{	
								$admin = $row['admin'];
								if($admin == 1)
								{
									if($connection->query("INSERT INTO dyzury_pracownikow VALUES (NULL, '$id', '$employee_id', 1, 1)"))
									{					
										header('Location: /Shifts/shift.php');	
									}
									else
									{
										throw new Exception($connection->errno);
									}
								}
								else
								{ 
									if($connection->query("INSERT INTO dyzury_pracownikow VALUES (NULL, '$id', '$employee_id', 0, 1)"))
									{					
										header('Location: /Shifts/shift.php');	
									}
									else
									{
										throw new Exception($connection->errno);
									}
								}

							}
						}
						else $_SESSION['e_password'] = "Proszę potwierdzić hasłem!";
					}			
				}
				else $can_register = false;
			}
			else $can_register_date = false;
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
	<title>Potwierdzanie usunięcia dyżuru</title>

	
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
					<div>Potwierdzanie zgloszenia zarejestrowania/zarejestrowania się na dyżur</div>
				</h3>
				
				<form method="post">
					<div class="form-group">
						<label>Potwierdź zgloszenie zarejestrowania/zarejestrowanie się na dyżur</label>
						<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" 					
					<?php 
						echo !$can_register ? "disabled" : "";
					?> />	
					</div>
					  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] . "</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
					
					<div>
						<button type="submit" class="btn btn-primary"<?php 
							echo !$can_register ? "disabled" : "";
							echo !$can_register_date ? "disabled" : "";
						?>>ZATWIERDŹ</button>
						<a href="/Shifts/shift.php" class="btn btn-primary">ANULUJ</a>
					</div>
				</form>

				<?php 
					echo "<br />";
					echo $can_register ? "" : "<div class='alert alert-danger' role='alert'>
						Brak wolnych miejsc!
					</div>";			
				?>				
				
				<?php 
					echo $can_register_date ? "" : "<div class='alert alert-danger' role='alert'>
						Nie można już zarejestrować na dyżur!
					</div>";			
				?>
				
				<?php 
					echo $can_register_date2 ? "" : "<div class='alert alert-danger' role='alert'>
						Nie możesz zarejestrować się na ten dyżur!
					</div>";			
				?>
				
			</div>
		</div>
	</div>
</body>
</html>


