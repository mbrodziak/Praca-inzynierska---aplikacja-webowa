<?php
	session_start();
	
	if(!isset($_SESSION['signed']))
	{
		header('Location: index.php');
		exit();
	}
	
	$ready = true;
	
	if(isset($_POST['old_pass']))
	{	
		$old_pass = $_POST['old_pass'];
		$new_pass = $_POST['new_pass'];
		$repeat_pass = $_POST['repeat_pass'];
	
		if($new_pass == NULL)
		{
			$ready = false;
			$_SESSION['e_new_pass'] = "Podaj nowe hasło";
		}
		
		if (strlen($new_pass) < 8 || strlen($new_pass) > 20)
		{
			$ready = false;
			$_SESSION['e_new_pass'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if($new_pass != $repeat_pass)
		{
			$ready = false;
			$_SESSION['e_repeat_pass'] = "Podane hasła nie są identyczne!";
		}
			
		$hash_pass = password_hash($new_pass, PASSWORD_DEFAULT);		
	

		require_once "/xampp/htdocs/Dyzury/connect.php";
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
				$login = $_SESSION['login'];
				$result = $connection->query("SELECT * FROM pracownicy where login = '$login'");
					
				if (!$result) throw new Exception($connection->error);
					
				$row = $result->fetch_assoc();
				
				if(!empty($old_pass))
				{
					if(!password_verify($old_pass, $row['haslo']))
					{
						$ready = false;
						$_SESSION['e_old_pass'] = "Podane hasło jest nieprawdłowe!";
					}
					
					if($old_pass == $new_pass)
					{
						$ready = false;
						$_SESSION['e_new_pass'] = "Hasło jest takie same jak poprzednie. Podaj inne hasło!";
					}							
				}
							
				if ($ready == true)
				{
					if(!empty($old_pass))
					{
						if ($connection->query("UPDATE pracownicy set haslo = '$hash_pass' where login = '$login'"))
						{
							$_SESSION['succesChanged'] = true;
							$_SESSION['passChanged'] = true;
							header('Location: /Dyzury/Employees/Edit/changedData.php');
						}
						else
						{
							throw new Exception($connection->error);
						}
					}
				}				
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera!</span>';
			echo '<br />Informacja developerska: '.$e;
		}				
	}
?>


<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zmień hasło</title>
	
	<link rel="stylesheet" href="/Dyzury/Style/style.css" type="text/css" />
	<link rel="stylesheet" href="fontello/css/fontello.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	<script>
		
		
	
	</script>
	
</head>

<body>
	
	<div class="header">
		Edytuj dane
	</div>
	
	<div class="container">
	
		<div class="list"> 
			<div class="fulfillment"></div>

			<a href="/Dyzury/signed.php" class="choose_option">
				<div class="option">
					Strona główna
				</div>
			</a>
			
			<a href="/Dyzury/Employees/profil.php" class="choose_option">
				<div class="option">
					Profil
				</div>
			</a>
			
			<a href="/Dyzury/Shifts/shift.php" class="choose_option">
				<div class="option">
					Dyżury
				</div>
			</a>
			
			<?php
				if($_SESSION['admin'] == 1)
				{
					echo '<a href="/Dyzury/Shifts/New/newShift.php" class="choose_option">
							<div class="option">
								Dodaj dyżur
							</div>
						</a>
						
						<a href="/Dyzury/Employees/New/newEmployee.php" class="choose_option">
							<div class="option">
								Dodaj pracownika
							</div class="option">
						</a>
						
						<a href="/Dyzury/Employees/Permissions/givePermission.php" class="choose_option">
							<div class="option">
								Nadaj uprawnienia
							</div class="option">
						</a>
						
						<a href="/Dyzury/Employees/Permissions/receivePermission.php" class="choose_option">
							<div class="option">
								Odbierz uprawnienia
							</div class="option">
						</a>';	
				}			
			?>
						
			<a href="/Dyzury/Employees/cadre.php" class="choose_option">
				<div class="option">
					Kadra
				</div>
			</a>
			
			<a href="/Dyzury/logout.php" class="logout">
				<div class="logOut">
					Wyloguj się 
				</div>
			</a>
			
		</div>
		
		<div class="no_name_yet">
	
			<form method="post" novalidate>
				<div class="change">
			
					<input type="password" name="old_pass" id="old_pass" placeholder="Stare hasło" /> <br /> 
			
					<?php
						if (isset($_SESSION['e_old_pass']))
						{
							echo '<div class="error">'.$_SESSION['e_old_pass'].'</div>';
							unset($_SESSION['e_old_pass']);
					}?> 

					
					<input type="password" name="new_pass" id="new_pass" placeholder="Nowe hasło" /> <br /> 
			
					<?php
						if (isset($_SESSION['e_new_pass']))
						{
							echo '<div class="error">'.$_SESSION['e_new_pass'].'</div>';
							unset($_SESSION['e_new_pass']);
					}?> 
					
					<input type="password" name="repeat_pass" id="repeat_pass" placeholder="Powtórz hasło" /> <br /> 
			
					<?php
						if (isset($_SESSION['e_repeat_pass']))
						{
							echo '<div class="error">'.$_SESSION['e_repeat_pass'].'</div>';
							unset($_SESSION['e_repeat_pass']);
					}?> 
			
					<input type="submit" id="confirm" value="ZATWIERDŹ" />
										
				</div>			
			</form>
			<div class="cancel"><a href="/Dyzury/Employees/Edit/changeDataChoice.php"><input type="submit" id="cancel" value="ANULUJ" /></a></div>
		</div>
	</div>
</body>
</html>