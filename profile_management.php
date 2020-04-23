<?php
    include_once('ProfileUtilities.php');
    session_start();
    $_SESSION['full-name-error'] = "";
    $_SESSION['address1-error'] = "";
    $_SESSION['address2-error'] = "";
    $_SESSION['city-error'] = "";
    $_SESSION['zipcode-error'] = "";
    $_SESSION['submit-error'] = "";
    $_SESSION['state-error'] = "";
    $utils = new ProfileFunctions();
    $utils->SelectQuery($_SESSION['active-user']);
    $utils->GrabResults();
    
    if(isset($_POST['register_profile'])){

        $_SESSION['full-name-error'] = $utils->ValidFullName($_POST['fullname']);
        $_SESSION['address1-error'] = $utils->ValidAddress1($_POST['address1']);
        $_SESSION['address2-error'] = $utils->ValidAddress2($_POST['address2']);
        $_SESSION['city-error'] = $utils->ValidCity($_POST['city']);
        $_SESSION['zipcode-error'] = $utils->ValidZip($_POST['zipcode']);
        $_SESSION['state-error'] = $_POST['state'] == "" ? "Please choose your state!" : "";
        

        if(!$_SESSION['full-name-error'] && 
        !$_SESSION['address1-error'] && 
        !$_SESSION['address2-error'] && 
        !$_SESSION['city-error'] && 
        !$_SESSION['zipcode-error'] && 
        !$_SESSION['state-error']){
            unset($_POST['register_profile']);

            if($utils->IsNewUser()){
                $_POST['username'] = $_SESSION['active-user'];
                
                foreach ($_POST as $key => $value) {
                    echo "Key: $key; Value: $value\n";
                }
                
                $res_insert = $utils->InsertQuery($_POST);
                
                if($res_insert){
                    $data = ["isnewuser"=>'No'];
                    $username_key = ["username"=>$_SESSION['active-user']];
                    $res_update = $utils->UpdateTable("logins",$data,$username_key);
                    if($res_update){
                        header('location: quote_history.php');
                    }
                    else{
                        $_SESSION['submit-error'] = "error when updating 'isnewuser' status to DB!";
                    }
                }
                else{
                    $_SESSION['submit-error'] = "error when submitting profile to DB!";
                }
            }
            else{
                $username_key = ["username"=>$_SESSION['active-user']];
                $res = $utils->UpdateTable("client_information",$_POST,$username_key);
                if($res){
                    header('location: quote_history.php');
                }
                else{
                    $_SESSION['submit-error'] = "error when updating profile to DB!";
                }
            }

        }

    }

?>



<!DOCTYPE html>
<html  lang="en-US">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head>
		<!-- title of page -->
		<title>Profile Page</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<!-- header of page -->
		<div class="links_header">
			<h1>Profile Management</h1>
		</div>

        <?php $utils->ShowMenu();?>

        <div class="header">
			<h2>Profile</h2>
		</div>
		

        <!-- creating HTTP POST method to allow DB variables in PHP portion of code to recieve values from HTML fields -->
		<form method="POST" action="profile_management.php">
			<!-- create required email field -->
			<div class="input-group">
				<label><b><u>Full Name*</u></b></label>
                <input type="text" name="fullname" maxlength="50" value="<?php echo isset($_POST['fullname']) ? $_POST['fullname'] : '' ?>"  required />
                <div class="error-message">
                    <?php echo $_SESSION['full-name-error'] ?>
                </div>
			</div>
			<!--  creat required password fields -->
			<div class="input-group">
				<label><b><u>Address 1*</u></b></label>
                <input type="text" name="address1" maxlength="100" value="<?php echo isset($_POST['address1']) ? $_POST['address1'] : '' ?>" required />
                <div class="error-message">
                    <?php echo $_SESSION['address1-error'] ?>
                </div>
			</div>
			<!-- create password confirmation field -->
			<div class="input-group">
				<label><b><u>Address 2</u></b></label>
				<input type="text" name="address2" maxlength="100" value="<?php echo isset($_POST['address2']) ? $_POST['address2'] : '' ?>"/>
                <div class="error-message">
                    <?php echo $_SESSION['address2-error'] ?>
                </div>
			</div>
			<div class="input-group">
				<label><b><u>City*</u></b></label>
				<input type="text" name="city" maxlength="100" value="<?php echo isset($_POST['city']) ? $_POST['city'] : '' ?>"  required />
                <div class="error-message">
                    <?php echo $_SESSION['city-error'] ?>
                </div>
			</div>
            
			<div class="input-group">
				<label><b><u>State*</u></b></label>
                <select name="state">
                    <option value="">****</option>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="DC">District of Columbia</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                </select>
                <div class="error-message">
                    <?php echo $_SESSION['state-error'] ?>
                </div>
			</div>
			
            <div class="input-group">
				<label><b><u>Zipcode*</u></b></label>
				<input type="text" name="zipcode" maxlength="9" minlength="5" value="<?php echo isset($_POST['zipcode']) ? $_POST['zipcode'] : '' ?>" required />
                <div class="error-message">
                    <?php echo $_SESSION['zipcode-error'] ?>
                </div>
			</div>
            
            <div class="input-group">
                <button type="submit" name="register_profile" class="btn">Submit</button>
                <div class="error-message">
                    <?php echo $_SESSION['submit-error'] ?>
                </div>
			</div>
		</form>
	</body>
</html>
