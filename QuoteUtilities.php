<?php
include_once('connection.php');

class QuoteFunctions extends Connection{
    
    function __construct(){
        parent::__construct();
    }

    function ValidGallons($gals){
        $error_message = "";
        $gals_int = (int)$gals;
        
        if(preg_match('/\s/',$gals)){$error_message = "Gallons cannot contain a space!";}
        elseif($gals_int <= 0){$error_message = "Gallons must be greater than 5!";}
        elseif(preg_match('/[^0-9\d]/',$gals)){ $error_message = "No special or alphabetic characters in gallons!"; }
        
        return $error_message;
    }

    function PullAddress($username){
        $q = "SELECT address1, address2, city, zipcode FROM profile WHERE username = '$username';";
        $res = pg_query($this->dbconnection,$q);
        return $res ? true : false;
    }

    function PullQuotes($username){
        $q = "SELECT * FROM quotes WHERE username = '$username';";
        $res = pg_query($this->dbconnection,$q);
        return $res ? true : false;
    }

}

?>
