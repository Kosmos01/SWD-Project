<?php
include_once('connection.php');

class RegisterFunctions extends Connection{

    private $result;

    function __construct(){
        parent::__construct();
    }

    function SelectQuery($username){
        
        $q = "SELECT * FROM users WHERE username = '$username';";
        $this->result = pg_query($this->dbconnection,$q);
    }

    function InsertQuery($array){

        $res = pg_insert($this->dbconnection,"users",$array);
        return $res ? true : false;
    }
    
    function CheckUsername($username){
        
        $error_string = "";

        if(pg_num_rows($this->result) >= 1){$error_string = "username is already registered!"; }
        elseif(trim($username) != $username){$error_string = "No whitespaces allowed in username!";}
        elseif(preg_match('/[^a-zA-Z0-9\d]/', $username)){ $error_string = "No special characters in username!"; }
        
        return $error_string;
    }

    function PasswordMatch($password_1,$password_2){
        
        $error_string = "";
        
        if ($password_1 != $password_2){ $error_string = "passwords do not match!";}
        
        return $error_string;
    }

}


?>