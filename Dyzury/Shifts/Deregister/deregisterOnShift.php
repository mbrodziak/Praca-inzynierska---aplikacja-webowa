<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
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

		if ($connection->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{
			parse_str($_SERVER['QUERY_STRING'], $qs);
			$id = mysqli_real_escape_string($connection, $qs['shift_id']);
			$can_deregister = true;
			$can_deregister_date = true;
			
			$login = $_SESSION['login'];
			$result = $connection->query("SELECT id_pracownika, haslo, admin FROM pracownicy WHERE login = '$login'");
			
			if (!$result) throw new Exception($connection->error);
				
			$row = $result->fetch_assoc();
			$id_employee = $row['id_pracownika'];
			$admin = $row['admin'];
			
			$result2 = $connection->query("SELECT * FROM dyzury_pracownikow WHERE id_dyzuru = '$id' AND id_pracownika = $id_employee");
			
			if (!$result2) throw new Exception($connection->error);
			
			$row2 = $result2->fetch_assoc();
			$confirm = $row2['potwierdzone'];
			$register = $row2['zarejestrowanie'];
			
			$result3 = $connection->query("SELECT data_dyzuru FROM dyzury WHERE id_dyzuru = '$id'");
			if (!$result3) throw new Exception($connection->error);
			
			$row3 = $result3->fetch_assoc();
			$shift_date = $row3['data_dyzuru'];
			
			if($shift_date >= $next_week_date)
			{
				if(isset($_POST['confirm_pass_submit']) && !empty($_POST['confirm_pass']))
				{
					$password = $_POST['confirm_pass'];
					
					if(!password_verify($password, $row['haslo'])) $_SESSION['e_password'] = "Błędne hasło!";
					
					else
					{	
						if($admin == 1)
						{
							if($connection->query("DELETE FROM dyzury_pracownikow WHERE id_dyzuru = '$id' AND id_pracownika = $id_employee"))
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
							if($connection->query("UPDATE dyzury_pracownikow SET potwierdzone = '0', zarejestrowanie = '0' 
							WHERE id_dyzuru = '$id' AND id_pracownika = '$id_employee'"))
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
				else if (isset($_POST['confirm_pass'])) $_SESSION['e_password'] = "Proszę potwierdzić hasłem!";
				if(($confirm === "0") && ($register === "0")) $can_deregister = false;
				if(($confirm === "0") && ($register === "1")) $can_deregister = false;
			}
			else $can_deregister_date = false;
			
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
					<div>Potwierdzanie zgłoszenia wyrejestrowania/wyrejestrowania się z dyżuru</div>
				</h3>
				
				<form method="post">
					<div class="form-group">
						<label>Potwierdź zgloszenia wyrejestrowania/wyrejestrowanie się z dyżuru</label>
						<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło "  
					<?php 
						echo !$can_deregister ? "disabled" : "";
						echo !$can_deregister_date ? "disabled" : "";
					?>/>	
					</div>
					  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] . "</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
					
					<div>
						<button type="submit" name="confirm_pass_submit" class="btn btn-primary"<?php 
						echo !$can_deregister ? "disabled" : "";
						echo !$can_deregister_date ? "disabled" : "";
					?>>ZATWIERDŹ</button>
						<a href="/Shifts/shift.php" class="btn btn-primary">ANULUJ</a>
					</div>
				</form>

				<?php
				if (isset($_SESSION['e_deregister']))
				{
					echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_deregister'] . "</div>";
					unset ($_SESSION['e_deregister']);
				}
				?> 
				
				<?php 
					echo "<br />";
					echo $can_deregister ? "" : "<div class='alert alert-danger' role='alert'>
						Oczekiwanie na zaakceptowanie zgłoszenia!
					</div>";			
				?>				
				
				<?php 
					echo $can_deregister_date ? "" : "<div class='alert alert-danger' role='alert'>
						Nie można się już wyrejestrować!
					</div>";			
				?>
			</div>
		</div>
	</div>
</body>
</html>


