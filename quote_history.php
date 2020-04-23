<?php
	include_once('QuoteUtilities.php');
	session_start();
	$utils = new QuoteFunctions();
	$utils->PullQuotes($_SESSION['active-user']);
?>


<!DOCTYPE html>
<html lang="en-US">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head>
		<!-- title of page -->
		<title>Quote History</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<!-- header of page -->
		<div class="links_header">
			<h1>Fuel Quote History</h1>
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
		
		<table>
            <t>
				<th><u>Quote Date</u></th>
				<th><u>Gallons Requested</u></th>
				<th><u>Delivery Address</u></th>
				<th><u>Delivery Date</u></th>
				<th><u>Suggested Price/Gallon</u></th>
				<th><u>Total Amount Due</u></th>
			</t>
			<?php $utils->PrintQuotes();?>
        </table>
	</body>