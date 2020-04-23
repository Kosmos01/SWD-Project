<?php
use PHPUnit\Framework\TestCase;
#include('LoginUtilities.php');
include_once('connection.php');
include('LoginUtilities.php');
include('RegisterUtilities.php');
include('ProfileUtilities.php');
include('QuoteUtilities.php');
class ProjectTest extends TestCase
{


    public function testLoginFunctions(){
        $utils = new LoginFunctions();
        $this->assertEquals(true,$utils->IsConnected());
        
        // user registered 
        $utils->SelectQuery('afsheen1995');
        $this->assertEquals(true,$utils->IsRegistered());
        
        // grabbing row from $results query
        $utils->GrabResults();

        // password doesnt match
        $this->assertEquals(false,$utils->PasswordMatch('incorrectPassword'));
    
        // password does match
        $this->assertEquals(true,$utils->PasswordMatch('password$123'));
        
        // checks if new user -- true
        $this->assertEquals(false,$utils->IsNewUser());

        $status = $utils->IsNewUser();

        // redirects to profile page since 'afsheen' is a new user      
        $this->assertEquals('location : profile_management.php',$utils->PageRedirect(true));
        
        // redirects to quote history page     
        $this->assertEquals('location : quote_history.php',$utils->PageRedirect($status));
        
        // user not registered
        $utils->SelectQuery('sean');
        $this->assertEquals(false,$utils->IsRegistered());
    }


    public function testRegistrationFunctions(){
        $utils = new RegisterFunctions();
        
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());

        // username is already in DB
        $utils->SelectQuery('afsheen1995');
        $this->assertEquals("username is already registered!",$utils->CheckUsername('afsheen1995'));
        
        // username containing special chars.
        $utils->SelectQuery('InDatabase!!!');
        $this->assertEquals("No special characters in username!",$utils->CheckUsername('InDatabase!!!'));

        // username containing whitespaces
        $utils->SelectQuery('not InDatabase   ');
        $this->assertEquals("No whitespaces allowed in username!",$utils->CheckUsername('not InDatabase   '));
    
        // username acceptable for registration and not in DB
        $utils->SelectQuery('InDatabase_123');
        $this->assertEquals("",$utils->CheckUsername('InDatabase_123'));

        // simple password match
        $this->assertEquals("",$utils->PasswordMatch('Password1','Password1'));
        $this->assertEquals("passwords do not match!",$utils->PasswordMatch('Password1','Password2'));
    
        // insert into DB  
        $insert_array = ["username"=>'billybob',"passwrd"=>'somePass',"isnewuser"=>"Yes"];
        $this->assertEquals("Success! You are now registered!",$utils->InsertQuery($insert_array));
    }

    public function testProfileManagementFunctions(){
        $utils = new ProfileFunctions();
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());

        // testing if new user -- if barely registering should be true
        $utils->SelectQuery('billybob');
        $utils->GrabResults();
        $this->assertEquals(true,$utils->IsNewUser());

        // checks if name is valid
        $this->assertEquals("There cannot be more than a total of two spaces to seperate your full name! Ex. first (middle) last",$utils->ValidFullName('super   cool'));
        $this->assertEquals("You must have at least a first and last name!",$utils->ValidFullName('daryl'));
        $this->assertEquals("You must have at least a first and last name!",$utils->ValidFullName('daryl'));
        $this->assertEquals("Names cannot have any numbers!",$utils->ValidFullName('daryl123 johnson'));
        $this->assertEquals("Names cannot have more than 1 hyphen per name!",$utils->ValidFullName('daryl johnson--smith bob'));
        $this->assertEquals("No special characters in names!",$utils->ValidFullName('daryl$$$ johnson'));
        $this->assertEquals("",$utils->ValidFullName('daryl smith johnson'));



        // check if zip is valid
        $this->assertEquals("Zip cannot contain a space!",$utils->ValidZip('7 541'));
        $this->assertEquals("No special or alphabetic characters in zip!",$utils->ValidZip('885!2'));
        $this->assertEquals("No special or alphabetic characters in zip!",$utils->ValidZip('aa852'));
        $this->assertEquals("",$utils->ValidZip('11424'));

        //checks if valid city
        $this->assertEquals("Cities cannot have more than 1 hyphen",$utils->ValidCity('Ivano--Frankivsk'));
        $this->assertEquals("Cities cannot have more than 2 spaces",$utils->ValidCity('Ivano      Frankivsk'));
        $this->assertEquals("Cities cannot have any numbers!", $utils->ValidCity('dallas123'));
        $this->assertEquals("No special characters in city name!",$utils->ValidCity('houston!@#$'));
        $this->assertEquals("",$utils->ValidCity('Houston'));

        //check if valid address1
        $this->assertEquals("Cannot have tabs to seperate names!",$utils->ValidAddress1('some   name'));
        $this->assertEquals("Cannot have only one word in address1!",$utils->ValidAddress1('lockheart'));
        $this->assertEquals("There cannot be more than a total of three spaces to seperate your address! Ex. 1234 royal sonesta ln",$utils->ValidAddress1('123 lockheart avenue st. yes'));
        $this->assertEquals("Cannot have more than 1 hyphen in street number!",$utils->ValidAddress1('123--4 lockheart avenue st.'));
        $this->assertEquals("Street number is required and should only consists of numbers and at most 1 hyphen",$utils->ValidAddress1('lockheart avenue st.'));
        $this->assertEquals("Street number should be less than a length of 6",$utils->ValidAddress1('4444444444 lockheart avenue st.'));
        $this->assertEquals("",$utils->ValidAddress1('1234 lockheart st.'));

        //check if valid address2
        $this->assertEquals("Cannot have tabs to seperate names!",$utils->ValidAddress2('apt.   124'));
        $this->assertEquals("Cannot have only one word in address2! Must designate unit abbreviation",$utils->ValidAddress2('123'));
        $this->assertEquals("There cannot be more than one space to seperate your address2! Ex. APT 3125, bldg. 5423, dept ABC",$utils->ValidAddress2('apt.   123'));
        $this->assertEquals("Only accepting abbreviates such as apt, bldg, dept, fl, ste, unit, dept, rm",$utils->ValidAddress2('apartment 1234'));
        $this->assertEquals("",$utils->ValidAddress2('bldg. ABC'));

        // checks insert to profile table -- not all the fields, but just showing it inserts correctly
        $insert_array = ["username"=>'billybob',"fullname"=>'bobby phillips',"address1"=>'1234 easy st.',"address2"=>'apt. 234',"city"=>'Houston',"state"=>'TX',"zipcode"=>'44124'];
        $this->assertEquals(true,$utils->InsertQuery($insert_array));
        // checks update to profile/user table
        $new_vals = ["isnewuser"=>'No'];
        $key_vals = ["username"=>'billybob'];
        $this->assertEquals(true,$utils->UpdateTable('users',$new_vals,$key_vals));


    }

    public function testQuoteFormFunctions(){
        $utils = new QuoteFunctions();
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());
        
        
        // validate gallon format

        // testing if address is pulled correctly
        $this->assertEquals(true,$utils->PullAddress('afsheen1995'));
        
        // testing if pull quotes is done correctly
        $this->assertEquals(true,$utils->PullQuotes('afsheen1995'));
    

        $this->assertEquals(0.01,$utils->IsFirstQuote());
        $this->assertEquals(0.02.$utils->IsInState());

        $this->assertEquals("Deliver date cannot be set before today's date!",$utils->ValidDate('01/02/2020'));
        $this->assertEquals("",$utils->ValidDate('04/25/2020'));

        $this->assertEquals(0.03,$utils->IsSummer());

        $utils->GetGals(500);

        $this->assertEquals(0.03,$utils->GallonsRequestedFactor());

    }

}
?>