<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
			
	if (isset($_POST['email']))
	{
		$ready = true;
		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$birthday = $_POST['birthday'];
		$email = $_POST['email'];
		$phone_number = $_POST['phone_number'];	
		$login = $_POST['login'];
		$pass = $_POST['pass'];
		
		$polish_char[0] = "ą"; 
		$polish_char[1] = "ć"; 
		$polish_char[2] = "ę"; 
		$polish_char[3] = "ł"; 
		$polish_char[4] = "ń"; 
		$polish_char[5] = "ó"; 
		$polish_char[6] = "ś"; 
		$polish_char[7] = "ź"; 
		$polish_char[8] = "ż"; 
		

		if(is_numeric($name))
		{
			$ready = false;
			$_SESSION['e_name'] = "Imię nie może być liczbą!";
		}
		
		if(is_numeric($surname))
		{
			$ready = false;
			$_SESSION['e_surname'] = "Nazwisko nie może być liczbą!";
		}
		
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if (filter_var($emailB, FILTER_VALIDATE_EMAIL == false) || ($emailB != $email))
		{
			$ready = false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
		}
							
		if (strlen($login) < 8 || strlen($login) > 30)
		{
			$ready = false;
			$_SESSION['e_login'] = "Login musi posiadać od 8 do 30 znaków!";
		}
				
		for($i = 0; $i < 9; $i++)
		{
			if(strpos($login, $polish_char[$i]))
			{
				$ready = false;
				$_SESSION['e_login'] = "Login nie może zawierać polskich znaków!";
			}
		}
		
		if (strlen($pass) < 8 || strlen($pass) > 20)
		{
			$ready = false;
			$_SESSION['e_pass'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		$hash_pass = password_hash($pass, PASSWORD_DEFAULT);
		
		$_SESSION['rem_name'] = $name;
		$_SESSION['rem_surname'] = $surname;
		$_SESSION['rem_birhday'] = $birthday;
		$_SESSION['rem_email'] = $email;
		$_SESSION['rem_phone'] = $phone_number;
		$_SESSION['rem_login'] = $login;
		
		if($phone_number == NULL)
		{
			$phone_number = "000000000";
		}
		
		$phone = (string) $phone_number;
		
		if (isset($_POST['admin']))
		{
			$_SESSION['rem_admin'] = 1;
			$admin2 = 1;
		}
		else
		{
			$_SESSION['rem_admin'] = 0;
			$admin2 = 0;
		}	
	
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
				$result = $connection->query("SELECT id_pracownika FROM pracownicy where login = '$login'");
				
				if (!$result) throw new Exception($connection->error);
				
				$howmuch_login = $result->num_rows;
				if($howmuch_login > 0)
				{
					$ready = false;
					$_SESSION['e_login'] = "Istnieje już pracownik o takim loginie!";
				}
				
				$result = $connection->query("SELECT id_pracownika FROM pracownicy where adres_email = '$email'");
				
				if (!$result) throw new Exception($connection->error);
				
				$howmuch_email = $result->num_rows;
				if($howmuch_email > 0)
				{
					$ready = false;
					$_SESSION['e_email'] = "Istnieje już pracownik o takim adresie e-mail!";
				}
				
				if ($ready == true)
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
							if ($connection->query("INSERT INTO pracownicy VALUES (NULL, '$name', '$surname', '$birthday', '$email', '$phone', '$login', 
							'$hash_pass', '$admin2', '0000-00-00 00:00:00')"))
							{
								header('Location: /Employees/cadre.php');
							}	
							else
							{
								throw new Exception($connection->error);
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
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie praocownika w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}
	}
		
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
	<title>Dodaj nowego pracownika</title>
	
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
					<div>Dodaj pracownika</div>
				</h3>
	
				<form method="post">
				  <div class="form-group">				  
					<label>Imię</label>
					<input type="text" class="form-control" name="name" id="name" placeholder="Imię" required value="<?php
					if (isset($_SESSION['rem_name']))
					{
						echo $_SESSION['rem_name'];
						unset($_SESSION['rem_name']);
					}?>" /> 
				  </div>
				  
					<?php
					if (isset($_SESSION['e_name']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_name'] .	"</div>";
						unset ($_SESSION['e_name']);
					}
					?> 
					
				  <div class="form-group">
					<label>Nazwisko</label>
					<input type="text" class="form-control" name="surname" id="surname" required placeholder="Nazwisko" value="<?php
					if (isset($_SESSION['rem_surname']))
					{
						echo $_SESSION['rem_surname'];
						unset($_SESSION['rem_surname']);
					}?>"/> 
				  </div>
				  
					<?php
					if (isset($_SESSION['e_surname']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_surname'] .	"</div>";
						unset ($_SESSION['e_surname']);
					}
					?> 
					
				  
				  <div class="form-group">
					<label>Data urodzenia</label>
					<input type="date" class="form-control" name="birthday" id="birthday" max="1999-12-31" value="<?php
					if (isset($_SESSION['rem_birhday']))
					{
						echo $_SESSION['rem_birhday'];
						unset($_SESSION['rem_birhday']);
					}?>"/> 
				   </div>
				  
				  <div class="form-group">
					<label>Adres e-mail</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="Adres e-mail" required value="<?php
					if (isset($_SESSION['rem_email']))
					{
						echo $_SESSION['rem_email'];
						unset($_SESSION['rem_email']);
					}?>" />  		
				  </div>
				  
				  	<?php
					if (isset($_SESSION['e_email']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_email'] . "</div>";
						unset ($_SESSION['e_email']);
					}
					?> 
				  
				  <div class="form-group">
					<label>Numer telefonu</label>
					<input type="tel" class="form-control" name="phone_number" id="phone_number" placeholder="Numer telefonu (opcjonalnie)" pattern="[0-9]{9}" value="<?php
					if (isset($_SESSION['rem_phone']))
					{
						echo $_SESSION['rem_phone'];
						unset($_SESSION['rem_phone']);
					}?>"/> 
				  </div>
				  
				  <div class="form-group">
					<label>Login</label>
					<input type="text" class="form-control" name="login" id="login" placeholder="Login" required value="<?php
					if (isset($_SESSION['rem_login']))
					{
						echo $_SESSION['rem_login'];
						unset($_SESSION['rem_login']);
					}?>"/> 
				  </div>
				  
					<?php
					if (isset($_SESSION['e_login']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_login'] . "</div>";
						unset ($_SESSION['e_login']);
					}
					?> 
				  
				  <div class="form-group">
					<label>Hasło</label>
					<input type="password" class="form-control" name="pass" id="pass" placeholder="Hasło" required /> 	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_pass']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_pass'] . "</div>";
						unset ($_SESSION['e_pass']);
					}
					?> 
				  
				  <div class="form-group form-check">
					<input type="checkbox" class="form-check-input" name="admin" id="admin"  <?php
					if (isset($_SESSION['rem_admin']) && $_SESSION['rem_admin'] == 1)
					{
						echo "checked";
						unset($_SESSION['rem_admin']);
					}
					else{
						echo "unchecked";
						unset($_SESSION['rem_admin']);
					}?>/> 
					<label> Admin </label> 
				  </div>
				  
				  					
				  <div class="form-group">
					<label>Potwierdź dodanie pracownika</label>
					<input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Hasło" />	
				  </div>
				  
					<?php
					if (isset($_SESSION['e_password']))
					{
						echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['e_password'] .	"</div>";
						unset ($_SESSION['e_password']);
					}
					?> 
				  
				  <button type="submit" class="btn btn-primary">DODAJ PRACOWNIKA</button>				  
				</form>
				
				<a href="/Employees/cadre.php" class="btn btn-primary" role="button" id="cancelEmployee">ANULUJ</a>
				
			</div>
		</div>
	</div>
</body>
</html>