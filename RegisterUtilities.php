<?php
include_once('connection.php');

class RegisterFunctions extends Connection{

    private $result;

    function __construct(){
        parent::__construct();
    }

    function SelectQuery($username){
        
        $q = "SELECT * FROM logins WHERE username = '$username';";
        $this->result = pg_query($this->dbconnection,$q);
    }

    function InsertQuery($array){

        $res = pg_insert($this->dbconnection,"logins",$array);
        return $res ? "Success! You are now registered!" : "Error when registering!";
    }
    
    function CheckUsername($username){

        $error_string = "";

        if(pg_num_rows($this->result) >= 1){$error_string = "username is already registered!"; }
        elseif(preg_match('/\s/',$username)){$error_string = "No whitespaces allowed in username!";}
        elseif(preg_match('/[^a-zA-Z0-9\d_]/', $username)){ $error_string = "No special characters in username!"; }
        
        return $error_string;
    }

    function PasswordMatch($password_1,$password_2){
        
        $error_string = "";
        
        if ($password_1 != $password_2){ $error_string = "passwords do not match!";}
        elseif(preg_match('/\s/',$password_1)){$error_string = "No whitespaces allowed in password!";}
        elseif(preg_match('/\s/',$password_2)){$error_string = "No whitespaces allowed in password!";}
        
        return $error_string;
    }

}


?>