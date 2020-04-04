<?php
include_once('connection.php');


class ProfileFunctions extends Connection{

    private $result;
    private $data;

    function __construct(){
        parent::__construct();
    }

    function SelectQuery($username){
        $q = "SELECT * FROM users WHERE username = '$username';";
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
        $res = pg_insert($this->dbconnection,"profile",$array);
        return $res ? true : false;
    }


    function ValidName($name){
        $error_message = "";

        $hyphen_count = 0;
        $char_array = str_split($name);
        foreach($char_array as $char){
            if($char == '-'){$hyphen_count++;}
            if($hyphen_count>1){break;}
        }

        if($hyphen_count > 1){$error_message = "Names cannot have more than 1 hyphen per name!";}
        elseif(preg_match('~[0-9]+~', $name)){ $error_message = "Names cannot have any numbers!";}
        elseif(preg_match('/\s/',$name)){$error_message = "No whitespaces allowed in names!";}
        elseif(preg_match('/[^a-zA-Z\-\d]/',$name)){ $error_message = "No special characters in names!"; }

        return $error_message;
    }

    function ValidZip($zip){
        $error_message = "";

        if(preg_match('/\s/',$zip)){$error_message = "Zip cannot contain a space!";}
        elseif(strlen($zip) != 5){$error_message = "Zip must be of length 5!";}
        elseif(preg_match('/[^0-9\d]/',$zip)){ $error_message = "No special or alphabetic characters in zip!"; }
        
        return $error_message;
    }

    function ValidCity($city){
        $error_message = "";

        $hyphen_count = 0;
        $whitespace = 0;
        $char_array = str_split($city);
        
        foreach($char_array as $char){
            if($char == '-'){$hyphen_count++;}
            if($char == ' '){$whitespace++;}
            if($hyphen_count>1 || $whitespace > 1){break;}
        }

        if($hyphen_count > 1){$error_message = "Cities cannot have more than 1 hyphen";}
        elseif($whitespace > 1){$error_message = "Cities cannot have more than 1 space";}
        elseif(preg_match('~[0-9]+~', $city)){ $error_message = "Cities cannot have any numbers!";}
        elseif(preg_match('/[^a-zA-Z\-\d]/',$city)){ $error_message = "No special characters in city name!"; }

        return $error_message;
    }

}


?>