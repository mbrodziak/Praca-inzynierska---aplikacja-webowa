<?php
	session_start();
		
	if (!isset($_SESSION['signed']))
	{
			header('Location: index.php');
			exit(); 
	}
	
	$next_week_date = date("Y-m-d", strtotime("+1 week"));
	//$shift_date = [];
	require_once __DIR__ . "/../connect.php";	
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
			
			$result = $connection->query("select data_dyzuru from dyzury where id_dyzuru = '$id' limit 1");
			if (!$result) throw new Exception($connection->error);
			$row = $result->fetch_assoc();
			$shift_date = $row['data_dyzuru'];
			
			if($shift_date >= $next_week_date)
			{
				$result = $connection->query("delete from dyzury where id_dyzuru = '$id' limit 1");
			
				if (!$result) throw new Exception($connection->error);
			
				$result = $connection->query("delete from dyzury_pracownikow where id_dyzuru = '$id' limit 1");
			
				if (!$result) throw new Exception($connection->error);
				
				header('Location: shift.php');
			}
			else
			{
				echo "Nie można usunąć dyżuru!";
			}								
		}
		$connection->close();
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie praocownika w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
?>



