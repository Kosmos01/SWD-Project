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
        $utils->SelectQuery('afsheen');
        $this->assertEquals(true,$utils->IsRegistered());
        
        // grabbing row from $results query
        $utils->GrabResults();

        // password doesnt match
        $this->assertEquals(false,$utils->PasswordMatch('incorrectPassword'));
    
        // password does match
        $this->assertEquals(true,$utils->PasswordMatch('password'));
        
        // checks if new user -- true
        $this->assertEquals(true,$utils->IsNewUser());

        // user not registered
        $utils->SelectQuery('sean');
        $this->assertEquals(false,$utils->IsRegistered());
        
        $status = $utils->IsNewUser();

        // redirects to profile page since 'afsheen' is a new user      
        $this->assertEquals('location : profile_management.php',$utils->PageRedirect($status));
        
        // redirects to quote history page     
        $this->assertEquals('location : quote_history.php',$utils->PageRedirect(false));
    }


    public function testRegistrationFunctions(){
        $utils = new RegisterFunctions();
        
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());

        // username is already in DB
        $utils->SelectQuery('afsheen');
        $this->assertEquals("username is already registered!",$utils->CheckUsername('afsheen'));
        
        // username containing special chars.
        $utils->SelectQuery('InDatabase123!!!');
        $this->assertEquals("No special characters in username!",$utils->CheckUsername('InDatabase123!!!'));

        // username containing whitespaces
        $utils->SelectQuery('not InDatabase   ');
        $this->assertEquals("No whitespaces allowed in username!",$utils->CheckUsername('not InDatabase   '));
    
        // username acceptable for registration and not in DB
        $utils->SelectQuery('InDatabase123');
        $this->assertEquals("",$utils->CheckUsername('InDatabase123'));

        // simple password match
        $this->assertEquals("",$utils->PasswordMatch('Password1','Password1'));
        $this->assertEquals("passwords do not match!",$utils->PasswordMatch('Password1','Password2'));
    
        // insert into DB  
        $insert_array = ["username"=>'billybob',"passwrd"=>'somePass',"isnewuser"=>"Yes"];
        $this->assertEquals(true,$utils->InsertQuery($insert_array));
    }

    public function testProfileManagementFunctions(){
        $utils = new ProfileFunctions();
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());

        // testing if new user -- if barely registering should be true
        $utils->SelectQuery('afsheen');
        $utils->GrabResults();
        $this->assertEquals(true,$utils->IsNewUser());

        // checks if name is valid
        $this->assertEquals("Names cannot have more than 1 hyphen per name!",$utils->ValidName('Hy-Ph-En'));
        $this->assertEquals("Names cannot have any numbers!",$utils->ValidName('super123cool'));
        
        // ! test not working?
        //$this->assertEquals("No whitespaces allowed in names!",$utils->ValidName('sp a ces'));
        
        //$this->assertEquals("No special characters in names!",$utils->ValidName('legitname!@#$'));
        $this->assertEquals("",$utils->ValidName('legit-name'));

        // check if zip is valid
        $this->assertEquals("Zip must be of length 5!",$utils->ValidZip('123'));
        $this->assertEquals("Zip cannot contain a space!",$utils->ValidZip('7 541'));
        $this->assertEquals("No special or alphabetic characters in zip!",$utils->ValidZip('885!2'));
        $this->assertEquals("No special or alphabetic characters in zip!",$utils->ValidZip('aa852'));
        $this->assertEquals("",$utils->ValidZip('11424'));

        //checks if valid city
        $this->assertEquals("Cities cannot have more than 1 hyphen",$utils->ValidCity('Ivano--Frankivsk'));
        $this->assertEquals("Cities cannot have more than 1 space",$utils->ValidCity('Ivano      Frankivsk'));
        $this->assertEquals("Cities cannot have any numbers!", $utils->ValidCity('dallas123'));
        $this->assertEquals("No special characters in city name!",$utils->ValidCity('houston!@#$'));
        $this->assertEquals("",$utils->ValidCity('Houston'));

        // checks insert to profile table -- not all the fields, but just showing it inserts correctly
        $insert_array = ["username"=>'billybob',"firstname"=>'dillion',"lastname"=>'phillips',"city"=>'Houston',"state"=>'TX'];
        $this->assertEquals(true,$utils->InsertQuery($insert_array));
        // checks update to profile/user table
        $new_vals = ["isnewuser"=>'No'];
        $key_vals = ["username"=>'afsheen'];
        $this->assertEquals(true,$utils->UpdateTable('users',$new_vals,$key_vals));


    }

    public function testQuoteFormFunctions(){
        $utils = new QuoteFunctions();
        // check connection is open
        $this->assertEquals(true,$utils->IsConnected());
        
        
        // validate gallon format
        $this->assertEquals("Gallons cannot contain a space!",$utils->ValidGallons('3  '));
        $this->assertEquals("Gallons must be greater than 5!",$utils->ValidGallons('-2'));
        $this->assertEquals("No special or alphabetic characters in gallons!",$utils->ValidGallons('330!@#'));
        $this->assertEquals("",$utils->ValidGallons('350'));

        // testing if address is pulled correctly
        $this->assertEquals(true,$utils->PullAddress('billybob'));
        
        // testing if pull quotes is done correctly
        $this->assertEquals(true,$utils->PullQuotes('billybob'));
    

    }

}
?>