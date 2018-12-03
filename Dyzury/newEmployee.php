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
		
		if($name == NULL)
		{
			$ready = false;
			$_SESSION['e_name'] = "Podaj imię!";
		}
		
		if(is_numeric($name))
		{
			$ready = false;
			$_SESSION['e_name'] = "Imię nie może być liczbą!";
		}
		
		if($surname == NULL)
		{
			$ready = false;
			$_SESSION['e_surname'] = "Podaj nazwisko!";
		}
		
		if(is_numeric($surname))
		{
			$ready = false;
			$_SESSION['e_surname'] = "Nazwisko nie może być liczbą!";
		}
		
		//$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

		if(empty($email))
		{
			$ready = false;
			$_SESSION['e_email'] = "Podaj adres e-mail!";
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
		
		$pass_length = strlen($pass);
		
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
	
		require_once "connect.php";
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
					$_SESSION['e_login'] = "Istnieje już pracownik o takim loginie";
				}
				
				$result = $connection->query("SELECT id_pracownika FROM pracownicy where adres_email = '$email'");
				
				if (!$result) throw new Exception($connection->error);
				
				$howmuch_email = $result->num_rows;
				if($howmuch_email > 0)
				{
					$ready = false;
					$_SESSION['e_email'] = "Istnieje już pracownik o takim adresie e-mail";
				}
				
				if ($ready == true)
				{
					$_SESSION['emp_name'] = $name;
					$_SESSION['emp_surname'] = $surname;
					$_SESSION['emp_birthday'] = $birthday;
					$_SESSION['emp_email'] = $email;
					$_SESSION['emp_phone'] = $phone;
					$_SESSION['emp_login'] = $login;
					$_SESSION['emp_pass'] = $hash_pass;
					$_SESSION['emp_admin'] = $admin2;
					$_SESSION['ready'] = true;
					
					header('Location: confirmEmployee.php');
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
	<title>Dodaj nowego pracownika</title>
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script>
		
		
	
	</script>
	
</head>

<body>
	
	<div class="header">
		DODAJ NOWEGO PRACOWNIKA 
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>

			<a href="signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>
			
			<a href="newShift.php" class="choose_option">
				<div class="option">
					Dodaj dyżur
				</div>
			</a>
			
			<a href="newEmployee.php" class="choose_option">
				<div class="option">
					Dodaj pracownika
				</div class="option">
			</a>
			
			<a href="noAdmin.php" class="choose_option">
				<div class="option">
					Nadaj uprawnienia
				</div class="option">
			</a>
			
			<a href="Admin.php" class="choose_option">
				<div class="option">
					Odbierz uprawnienia
				</div class="option">
			</a>
			
			<a href="cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
		
		<div class="no_name_yet">
	
			<form method="post" novalidate>
				<div id="newEmployee">
					<input type="text" name="name" id="name" placeholder="Imię" value="<?php
					if (isset($_SESSION['rem_name']))
					{
						echo $_SESSION['rem_name'];
						unset($_SESSION['rem_name']);
					}?>" />
			
					<?php
						if (isset($_SESSION['e_name']))
						{
							echo '<div class="error">'.$_SESSION['e_name'].'</div>';
							unset ($_SESSION['e_name']);
						}
					?> 
			
					<input type="text" name="surname" id="surname" placeholder="Nazwisko" value="<?php
					if (isset($_SESSION['rem_surname']))
					{
						echo $_SESSION['rem_surname'];
						unset($_SESSION['rem_surname']);
					}?>"/> 
			
					<?php
						if (isset($_SESSION['e_surname']))
						{
							echo '<div class="error">'.$_SESSION['e_surname'].'</div>';
							unset ($_SESSION['e_surname']);
						}
					?> 
			
					<input type="text" name="enterBirthday" id="enterBirthday" value="Data urodzenia (opcjonalnie):" disabled /> 
			
					<input type="date" name="birthday" id="birthday" value="<?php
					if (isset($_SESSION['rem_birhday']))
					{
						echo $_SESSION['rem_birhday'];
						unset($_SESSION['rem_birhday']);
					}?>"/> 
			
					<input type="email" name="email" id="email" placeholder="Adres e-mail" value="<?php
					if (isset($_SESSION['rem_email']))
					{
						echo $_SESSION['rem_email'];
						unset($_SESSION['rem_email']);
					}?>" />  
			
					<?php
					if (isset($_SESSION['e_email']))
					{
						echo '<div class="error">'.$_SESSION['e_email'].'</div>';
						unset($_SESSION['e_email']);
					}?> 
			
					<input type="tel" name="phone_number" id="phone_number" placeholder="Numer telefonu (opcjonalnie)" pattern="[0-9]{9}" value="<?php
					if (isset($_SESSION['rem_phone']))
					{
						echo $_SESSION['rem_phone'];
						unset($_SESSION['rem_phone']);
					}?>"/> 
					
					<input type="text" name="login" id="login" placeholder="Login" value="<?php
					if (isset($_SESSION['rem_login']))
					{
						echo $_SESSION['rem_login'];
						unset($_SESSION['rem_login']);
					}?>"/> 
			
					<?php
						if (isset($_SESSION['e_login']))
						{
							echo '<div class="error">'.$_SESSION['e_login'].'</div>';
							unset($_SESSION['e_login']);
					}?> 
			
					<input type="password" name="pass" id="pass" placeholder="Hasło" /> <br /> 
			
					<?php
						if (isset($_SESSION['e_pass']))
						{
							echo '<div class="error">'.$_SESSION['e_pass'].'</div>';
							unset($_SESSION['e_pass']);
					}?> 
			
					<label><input type="checkbox" name="admin" id="admin"  <?php
					if (isset($_SESSION['rem_admin']) && $_SESSION['rem_admin'] == 1)
					{
						echo "checked";
						unset($_SESSION['rem_admin']);
					}
					else{
						echo "unchecked";
						unset($_SESSION['rem_admin']);
					}?>/> Admin </label> 			
			
					<input type="submit" id="addEmployee" value="DODAJ PRACOWNIKA" />	
				</div>			
			</form>
		</div>
	</div>
</body>
</html>