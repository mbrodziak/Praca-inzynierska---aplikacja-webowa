<?php
	session_start();
	
	if (isset($_SESSION['signed']) && $_SESSION['signed'] == true)
	{
		header('Location: signed.php');
		exit();
	}
?>



<!DOCTYPE HTML>
<html lang ="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" name="viewport" />
	<title>Panel logowania</title>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="/Assets/Style/style.css" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:400,900&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	

</head>
<body>
	<div id="loginPanel">
		<div class="container">
			<div class="row">
				<div class="col">
					<h3 class="d-flex flex-row justify-content-between">
						<div>PANEL LOGOWANIA</div>
					</h3>
		
					<form method="post" action="signIn.php">
					  <div class="form-group">				  
						<label>Login</label>
						<input type="text" class="form-control" name="login" id="login" placeholder="Login" /> 
					  </div>
					  
					  <div class="form-group">
						<label>Hasło</label>
						<input type="password" class="form-control" name="pass" id="pass" placeholder="Hasło" required /> 	
					  </div>
		
					  <button type="submit" class="btn btn-primary">ZALOGUJ SIĘ</button>	
					</form>
					<?php
						if(isset($_SESSION['error'])){
							echo "<div class='alert alert-danger my-3' role='alert'>" . $_SESSION['error'] . "</div>";
							unset ($_SESSION['error']);
						}
					?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>