<?php
session_start();
include('initial.php');
include("createDB.php");
include('account.php');

$account = new Account($con);
$wasSuccessful = false;

if(isset($_POST['registerButton'])){
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$confirmPassword = $_POST['confirmPassword'];
	$wasSuccessful = $account->register($fname,$lname,$username,$password,$confirmPassword);
}

if(isset($_POST['loginButton'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$loginSuccessful = $account->login($username,$password);

	if($loginSuccessful){
		$_SESSION["username"] = $username;
		header("Location:dashboard.php");
	}
}

function getValue($string){
	if(isset($_POST[$string])){
		echo $_POST[$string];
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Wallet Manager</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="register.css">
</head>
<body>
    <h1>WALLET MANAGER</h1>
		<form action="register.php" method = "POST" id="login">
			<h2>Login</h2>
			<?php if($wasSuccessful){
		       echo 'Successfully created an account';
	         } ?>
	         <?php 
				echo $account->displayError("Username doesn't exist");
				?>
			<div class="row">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-user"></i></span>	
				</div>
				<input type="text" name="username" id="username" placeholder="Username" required>	
			</div>
			<?php 
				echo $account->displayError("Incorrect Password");
				?>
			<div class="row">
				<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-key"></i></span>
				</div>
				<input type="password" name="password" id="password" placeholder="password" required>
			</div>
			<div class="row">
				<button type="submit" name="loginButton">LOGIN</button>
			</div>
			<div class=hasAccount>
				<span id="showRegister">Don't Have An Account? Click Here.</span>
			</div>
		</form>

	<div id="register" class="hidden">
		<form action="register.php" method="POST">
			<h2>Register Here</h2>
			<?php
				echo $account->displayError("Invalid First Name");
			?>
			<div class="row">
				<label for="fname">First Name:</label>
				<input type="text" name="fname" value="<?php getValue("fname");?>" placeholder="5-20 char" required>
			</div>
			<?php
				echo $account->displayError("Invalid Last Name");
				?>
			<div class="row">
				
				<label for="lname">Last Name:</label>
				<input type="text" name="lname" value="<?php getValue("lname"); ?>" placeholder="max 20 char"required>
			</div>
			<?php
				echo $account->displayError("Invalid Username");
				echo $account->displayError("Username Exists");
				?>
			<div class="row">
				
				<label for="username">UserName:</label>
				<input type="text" name="username" value="<?php getValue("username"); ?>" placeholder="max 20 char" required>
			</div>
			<?php
				echo $account->displayError("Longer Password required");
				?>
			<div class="row">
				
				<label for="Name">Password:</label>
				<input type="password" name="password" placeholder="min 6 char" required>
			</div>
			<?php
				echo $account->displayError("Passwords don't match");
				?>
			<div class="row">
				
				<label for="Name">Confirm Password:</label>
				<input type="password" name="confirmPassword" required>
			</div>
			<div class="row">
				<button type="submit" name="registerButton">REGISTER</button>
			</div>
			<div class=hasAccount>
				<span id="showLogin">Have An Account? Click Here.</span>
			</div>
		</form>
	</div>

	 <?php
	if(isset($_POST['registerButton'])){
		if(!$wasSuccessful){
		echo '<script>
		var hidden = document.querySelector(".hidden");
		var login = document.querySelector("#login");
		var register = document.querySelector("#register");
		register.classList.remove("hidden");
		document.querySelector("#login").classList.add("hidden");
		</script>';
		}
	} ?>

	<script type="text/javascript" src="register.js"></script>
</body>
</html>