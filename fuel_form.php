<?php
	include_once('QuoteUtilities.php');
	session_start();
	$utils = new QuoteFunctions();
	$utils->PullAddress($_SESSION['active-user']);
	$utils->GetAddress();
	$utils->PullQuotes($_SESSION['active-user']);
	$_SESSION['submit-error'] = "";
	$_SESSION['submit-success'] = "";
	$_SESSION['date-error'] = "";
	$_SESSION['suggested'] = null;
	$_SESSION['total'] = null;

	if(isset($_POST['suggested-price'])){
		$_SESSION['date-error'] = $utils->ValidDate($_POST['delivery-date']);
		if(!$_SESSION['date-error']){
			$utils->GetGallons($_POST['gals']);
			$_SESSION['suggested'] = $utils->SuggestPrice();
			$_SESSION['total'] = $utils->TotalPrice();
			
			$_SESSION['submit-gallons'] = $_POST['gals'];
			$_SESSION['submit-suggested'] = $_SESSION['suggested'];
			$_SESSION['submit-total'] = $_SESSION['total'];
			$_SESSION['submit-date'] = $utils->ReturnDate();
		}
		else{
			$_SESSION['submit-total'] = null;
			$_SESSION['submit-suggested'] = null;

		}
	}
	elseif(isset($_POST['submit-price'])){
		if(isset($_SESSION['submit-total']) && isset($_SESSION['submit-suggested'])){

			$res = $utils->SubmitQuote($_SESSION['active-user'],$_SESSION['submit-gallons'],$_SESSION['submit-date'],$_SESSION['submit-suggested'],$_SESSION['submit-total']);

			if($res){$_SESSION['submit-success'] = "Your quote has been recorded!";}
			else{$_SESSION['submit-error'] = "Your quote was not recorded correctly!";}

			$_SESSION['suggested'] = $_SESSION['submit-suggested'];
			$_SESSION['total'] = $_SESSION['submit-total'];

			$_SESSION['submit-suggested'] = null;
			$_SESSION['submit-total'] = null;
		}
		else{
			$_SESSION['submit-error'] = "You must click 'Get Price' first per each submitted quote!";
		}
	}


?>


<!DOCTYPE html>
<html  lang="en-US">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head>
		<!-- title of page -->
		<title>Fuel Quote Form</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<!-- header of page -->
		<div class="links_header">
			<h1>Quote Form</h1>
		</div>
		<div class="links_display">
			<!-- create a top navigation bar to go to homepage (this)
			or go to logout page (login page redirect) -->
			<div class="topnav">
	 	 		<a class="active" href="profile_management.php">Profile</a>
	 	 		<a class="active" href="quote_history.php">Quote History</a>
				<a class="active" href="fuel_form.php">Quote Form</a>
	 	 		<a class="active" href="login.php">Logout</a>
			</div> 
		</div class="links_indiv">
		
		<div class="header">
			<h2>Fuel Quote Form</h2>
		</div>
		
		<!-- creating HTTP POST method to allow DB variables in PHP portion of code to recieve values from HTML fields -->
		<form method="POST" action="fuel_form.php">
			<!-- create required email field -->
			<div class="input-group">
				<label><b><u>Gallons Requested*:</u></b></label>
                <input type="number" min="1" max="1000000000" placeholder="0" name="gals" value="<?php echo isset($_POST['gals']) ? $_POST['gals'] : '' ?>"  required />
                <div class="error-message">
                    <?php echo $email_error; ?>
                </div>
			</div>
			<!--  creat required password fields -->
			<div class="outputDB">
				<label><b><u>Delivery Address:</u></b></label>
                <p style="text-indent :5em;"><?php $utils->PrintAddress() ?></p>
			</div>
			<!-- create password confirmation field -->
			<div class="input-group">
				<label><b><u>Delivery Date*:</u></b></label>
				<input type="date" name="delivery-date" value=<?php echo isset($_POST['delivery-date']) ? $_POST['delivery-date'] : date("m/d/Y") ?>  required />
				<div class="error-message">
                    <?php echo $_SESSION['date-error']; ?>
                </div>
			</div>
			
            <div class="outputDB">
				<label><b><u>Suggested Price/Gallon:</u></b></label>
                <p style="text-indent :5em;"><?php echo isset($_SESSION['suggested']) ? "$" . $_SESSION['suggested'] : "********" ?></p>
                
			</div>

            <div class="outputDB">
				<label><b><u>Total Amount Due:</u></b></label>
				<p style="text-indent :5em;"><?php echo isset($_SESSION['total']) ? "$" . $_SESSION['total'] : "********" ?></p>
			</div>
            
			<div class="input-group">
                <button type="submit" name="suggested-price" class="btn">Get Price</button>
			</div>

            <div class="input-group">
                <button type="submit" name="submit-price" class="btn">Submit</button>
				<div class="error-message">
                    <?php echo $_SESSION['submit-error']; ?>
                </div>
				<div class="success-message">
                    <?php echo $_SESSION['submit-success']; ?>
                </div>
			</div>
		</form>
	</body>
</html>
