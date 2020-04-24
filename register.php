
<?php
    // connecting to database

	session_start();
	include_once('RegisterUtilities.php');
	
	$_SESSION['username-error'] = "";
    $_SESSION['password-error'] = "";
	$_SESSION['register-message'] = "";

	if(isset($_POST['register'])){
		
		$utils = new RegisterFunctions();
		
		/*
		if($utils->IsConnected()){
			echo 'connected';
		}
		else{
			echo 'not connected';
		}
		*/

		$utils->SelectQuery($_POST['username']);
		$_SESSION['username-error'] = $utils->CheckUsername($_POST['username']);
		
		if(!$_SESSION['username-error']){$_SESSION['password-error'] = $utils->PasswordMatch($_POST['password_1'],$_POST['password_2']);}

		if(!$_SESSION['username-error'] && !$_SESSION['password-error']) {
		
			$user_array = ["username"=>$_POST['username'],"passwrd"=>$_POST['password_1'],"isnewuser"=>'Yes'];
			$_SESSION['register-message'] = $utils->InsertQuery($user_array);
		}

		/*
		if($utils->IsRegistered()) { $_SESSION['username-error'] = "Username is already registered";}
		elseif(!$utils->PasswordMatch($_POST['password_1'],$_POST['password_2'])) { $_SESSION['password-error'] = "Passwords do not match"; }
		else{
			$connection = pg_connect("host=localhost dbname=postgres user=postgres password=brownCow01") or die("hello");

			$user_array = ["username"=>'trouble',"passwrd"=>'test',"isnewuser"=>'Y'];
			#$res = pg_insert($utils->,"users",$user_array);
			#$res = $utils->InsertQuery($user_array);
			if($res){echo 'success!';}
			else{echo 'shit!';}
		}
		*/
	}
?>

<!DOCTYPE html>
<html  lang="en-US">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head>
		<!-- title of page -->
		<title>Registration Form</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<!-- header of page -->
		<div class="header">
			<h2>Register</h2>
		</div>
		<!-- creating HTTP POST method to allow DB variables in PHP portion of code to recieve values from HTML fields -->
		<form method="POST" action="register.php">
			<!-- create required email field -->
			<div class="input-group">
				<label>Username</label>
                <input type="text" name="username" maxlength="25" required />
                <div class="error-message">
                    <?php echo $_SESSION['username-error']; ?>
                </div>
			</div>
			<!--  creat required password fields -->
			<div class="input-group">
				<label>Password</label>
                <input type="password" name="password_1" maxlength="20" required />
                <div class="error-message">
                    <?php echo $_SESSION['password-error']; ?>
                </div>
			</div>
			<!-- create password confirmation field -->
			<div class="input-group">
				<label>Confirm Password</label>
				<input type="password" name="password_2" maxlength="20" required />
			</div>
			<div class="input-group">
                <button type="submit" name="register" class="btn">Submit</button>
                <div class="success-message">
                    <?php echo $_SESSION['register-message'] ?>
                </div>
			</div>
			<!-- login page redirect -->
			<p>Already Registered? <a title="Sign in" href="login.php">Sign in here</a></p>
		</form>
	</body>
</html>
