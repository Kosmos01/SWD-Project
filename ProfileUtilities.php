<?php
include_once('connection.php');


class ProfileFunctions extends Connection{

    private $result;
    private $data;

    function __construct(){
        parent::__construct();
    }

    function SelectQuery($username){
        $q = "SELECT * FROM logins WHERE username = '$username';";
        $this->result = pg_query($this->dbconnection,$q);
    }
    
    function GrabResults(){
        $this->data = pg_fetch_row($this->result);
    }

    function IsNewUser(){
        
        if ($this->data[2] == "Yes"){
            return true;
        }
        else{
            return false;
        }
    }

    function UpdateTable($tablename,$value,$key){
        $res = pg_update($this->dbconnection,$tablename,$value,$key);
        return $res ? true : false;
    }

    
    function InsertQuery($array){
        $res = pg_insert($this->dbconnection,"client_information",$array);
        return $res ? true : false;
    }


    function ValidFullName($name){
        
        $error_message = "";
        $trimmedname = trim($name);

        if(substr_count($trimmedname,"\t") > 0){$error_message = "Cannot have tabs to seperate names!";}
        elseif(substr_count($trimmedname," ") == 0){$error_message = "You must have at least a first and last name!";}
        elseif(substr_count($trimmedname," ") > 2){$error_message = "There cannot be more than a total of two spaces to seperate your full name! Ex. first (middle) last";}

        if(!$error_message){
            $name_array = explode(" ",$trimmedname);

            foreach($name_array as $n){
                $error_message = $this->ValidName($n);
                if($error_message){break;}
            }
        }

        return $error_message;
    }

    function ValidAddress1($address1){
        $error_message = "";
        $trimmedaddress1 = trim($address1);

        if(substr_count($trimmedaddress1,"\t") > 0){$error_message = "Cannot have tabs to seperate names!";}
        elseif(substr_count($trimmedaddress1," ") == 0){$error_message = "Cannot have only one word in address1!";}
        elseif(substr_count($trimmedaddress1," ") > 3){$error_message = "There cannot be more than a total of three spaces to seperate your address! Ex. 1234 royal sonesta ln";}



        if(!$error_message){
            $address_array = explode(" ",$trimmedaddress1);
            
            if(substr_count($address_array[0],"-") > 1){$error_message="Cannot have more than 1 hyphen in street number!";}
            elseif(preg_match('/[^0-9\d-]/',$address_array[0])){$error_message = "Street number is required and should only consists of numbers and at most 1 hyphen";}
            elseif(strlen($address_array[0]) > 6){$error_message = "Street number should be less than a length of 6";}
            

        }

        return $error_message;
    }

    function ValidAddress2($address2){
        
        $error_message = "";

        if(!trim($address2)){
            return $error_message;
        }

        $acceptable_abbreviations = ["APT","APT.","apt","apt.","BLDG","BLDG.","bldg","bldg.","FL","FL.","fl","fl.",
        "STE","STE.","ste","ste.","UNIT","unit","RM","RM.","rm","rm.","DEPT","DEPT.","dept","dept."];
        $trimmedaddress2 = trim($address2);

        if(substr_count($trimmedaddress2,"\t") > 0){$error_message = "Cannot have tabs to seperate names!";}
        elseif(substr_count($trimmedaddress2," ") == 0){$error_message = "Cannot have only one word in address2! Must designate unit abbreviation and unit number/name";}
        elseif(substr_count($trimmedaddress2," ") > 1){$error_message = "There cannot be more than one space to seperate your address2! Ex. APT 3125, bldg. 5423, dept ABC";}

        if(!$error_message){
            $address_array = explode(" ",$trimmedaddress2);
            if(!in_array($address_array[0],$acceptable_abbreviations)){$error_message = "Only accepting abbreviates such as apt, bldg, dept, fl, ste, unit, dept, rm";}
        }

        return $error_message;

    }

    function ValidName($name){
        $error_message = "";

        if(substr_count($name,"-") > 1){$error_message = "Names cannot have more than 1 hyphen per name!";}
        elseif(preg_match('~[0-9]+~', $name)){ $error_message = "Names cannot have any numbers!";}
        elseif(preg_match('/[^a-zA-Z-]/',$name)){ $error_message = "No special characters in names!"; }

        return $error_message;
    }

    function ValidZip($zip){
        $error_message = "";

        if(preg_match('/\s/',$zip)){$error_message = "Zip cannot contain a space!";}
        elseif(preg_match('/[^0-9\d]/',$zip)){ $error_message = "No special or alphabetic characters in zip!"; }
        
        return $error_message;
    }

    function ValidCity($city){
        $error_message = "";
        $trimmedcity = trim($city);

        if(substr_count($trimmedcity,"-") > 1){$error_message = "Cities cannot have more than 1 hyphen";}
        elseif(substr_count($trimmedcity," ") > 2){$error_message = "Cities cannot have more than 2 spaces";}
        
        if(!$error_message){
            $city_array = explode(" ",$trimmedcity);

            foreach($city_array as $c){
                if(preg_match('~[0-9]+~', $c)){ $error_message = "Cities cannot have any numbers!";}
                elseif(preg_match('/[^a-zA-Z-.]/',$c)){ $error_message = "No special characters in city name!";}

                if($error_message){break;}
            }

        }

        return $error_message;
    }


    function ShowMenu(){
        if(!$this->IsNewUser()){
            echo "<div class='links_display'>";
                echo "<div class='topnav'>";
                    echo "<a class='active' href='profile_management.php'>Profile</a>";
                    echo "<a class='active' href='quote_history.php'>Quote History</a>";
                    echo "<a class='active' href='fuel_form.php'>Quote Form</a>";
                    echo "<a class='active' href='login.php'>Logout</a>";
                echo "</div>" ;
		    echo "</div class='links_indiv'>";
        }
    }


}


?>