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
			$id = mysqli_real_escape_string($connection, $qs['id']);
			$can_edit = true;
			
			$login = $_SESSION['login'];
			$result = $connection->query("SELECT id_pracownika, haslo, admin FROM pracownicy where login = '$login'");
			
			if (!$result) throw new Exception($connection->error);
				
			$row = $result->fetch_assoc();
	
			$result2 = $connection->query("select id_dyzuru from dyzury_pracownikow where id = '$id'");
			if (!$result2) throw new Exception($connection->error);
			
			$row2 = $result2->fetch_assoc();
			$id_shift = $row2['id_dyzuru'];
			
			$result3 = $connection->query("select ilosc_miejsc from dyzury where id_dyzuru = '$id_shift'");
			if (!$result3) throw new Exception($connection->error);
			
			$row3 = $result3->fetch_assoc();
			$shift_capacity = $row3['ilosc_miejsc'];

			$result4 = $connection->query("select * from dyzury_pracownikow where id_dyzuru = '$id_shift' 
			and (potwierdzone = 1 or (potwierdzone = 0 and zarejestrowanie = 0))");
			if (!$result4) throw new Exception($connection->error);
			
			$shift_busy = $result4->num_rows;
			
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
							if($connection->query("update dyzury_pracownikow set potwierdzone = '1' where id = '$id'"))
							{					
								header('Location: /Shifts/Register/applicationAdmin.php');	
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
			else $can_edit = false;
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
					<div>Potwierdzanie zgloszenia</div>
				</h3>
				
				<form method="post">
					<div class="form-group">
						<label>Potwierdź zgloszenie</label>
						<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" 					
					<?php 
						echo !$can_edit ? "disabled" : "";
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
					<button type="submit" class="btn btn-primary">ZATWIERDŹ</button>
					<a href="/Shifts/shift.php" class="btn btn-primary">ANULUJ</a>
				  </div>
				</form>	

				<?php 
					echo $can_edit ? "" : "<div class='alert alert-danger' role='alert'>
						Brak wolnych miejsc!
					</div>";			
				?>				
			</div>
		</div>
	</div>
</body>
</html>


