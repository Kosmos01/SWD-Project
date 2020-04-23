<?php
    // connecting to database
	session_start();
	include_once('LoginUtilities.php');	
	
	//variables for email error text displayed
    $_SESSION["username-error"] = "";
	
	//variables for password error text displayed
    $_SESSION["password-error"] = "";
		
			
	if(isset($_POST['login'])){
		
		$utils = new LoginFunctions();

		$utils->SelectQuery($_POST['username']);
		$utils->GrabResults();
		if(!$utils->IsRegistered()){ $_SESSION['username-error'] = "User is not registered"; }
		elseif(!$utils->PasswordMatch($_POST['password'])){ $_SESSION['password-error'] = "Incorrect password"; }
		else{

			$_SESSION['active-user'] = $_POST["username"];
			$user_status = $utils->IsNewUser();
			$utils->Disconnect();
			if($user_status){
				header('location: profile_management.php');
			}
			else{
				header('location: quote_history.php');
			} 
		}
    }
?>

<!-- comments for this section are available in index.html file -->

<!DOCTYPE html>
<html lang="en-US">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head style="background-color:#f8f8ff">
		<title>Login</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<div class="header">
			<h2>Sign in</h2>
		</div>
		<form method="POST" action="login.php">
			<div class="input-group">
				<label>Username</label>
                <input type="text" name="username" maxlength="25"value="<?php echo isset($_POST['username']) ? $_POST['username'] : '' ?>" required />
                <div class="error-message">
                    <?php echo $_SESSION['username-error']; ?>
                </div>
			</div>
			<div class="input-group">
				<label>Password</label>
                <input type="password" name="password" maxlength="20" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>" required />
                <div class="error-message">
                    <?php echo $_SESSION['password-error']; ?>
                </div>
			</div>
			<div class="input-group">
                <button type="submit" name="login" title="Login" class="btn">Login</button>
			</div>
            <p>Not registered? <a title="Register" href="register.php">Register here</a></p>
            <!--<p>Want to reset or forgot your password? <a title="Reset password" href="reset_password.php">Password Reset</a></p> -->

		</form>
	</body>
</html>
