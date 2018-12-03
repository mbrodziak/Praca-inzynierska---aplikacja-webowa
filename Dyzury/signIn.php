<?php
	
	session_start();
	
	$last_login = date("Y-m-d H:i:s");
	
	if (!isset($_POST['login']) || !isset($_POST['pass']))
	{
			header('Location: index.php');
			exit();
	}
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try
	{
		$connection = new mysqli($host, $db_user, $db_password, $db_name);
		$connection -> query ('SET NAMES utf8');
		$connection -> query ('SET CHARACTER_SET utf8_unicode_ci');
	
		if ($connection->connect_errno != 0){
			throw new Exception(mysqli_connect_errno());
		}
		else {
			$login = $_POST['login'];
			$pass = $_POST['pass'];
		
			$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		
			if ($result = $connection->query(
			sprintf("SELECT * FROM pracownicy WHERE login = '%s'", 
			mysqli_real_escape_string($connection, $login))))
			{
			
				$employees = $result->num_rows;
				if ($employees > 0){
				
					$row = $result->fetch_assoc();
				
					if (password_verify($pass, $row['haslo']))
					{
						
						$_SESSION['id_employee'] = $row['id_pracownika'];
						$_SESSION['name'] = $row['imie'];
						$_SESSION['surname'] = $row['nazwisko'];
						$_SESSION['birthday'] = $row['data_urodzenia'];
						$_SESSION['email'] = $row['adres_email'];
						$_SESSION['phone'] = $row['numer_telefonu'];
						$_SESSION['login'] = $row['login'];
						$_SESSION['pass'] = $row['haslo'];
						$_SESSION['admin'] = $row['admin'];
						
						$connection->query("UPDATE pracownicy set ostatnie_logowanie = '$last_login' where login = '$login'");
				
						unset($_SESSION['error']);
						$result->free_result();
						$_SESSION['signed'] = true;
						header('Location: signed.php'); 
					}
					else{
						$_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
						header('Location: index.php');
					}

				}
				else{
					$_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
				}				
			}
			else{
				throw new Exception(mysqli_connect_errno);
			}
			$connection->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! </span>';
		echo '<br />Informacja developerska: '.$e;
	}
?>		
