<?php
include_once('connection.php');
class LoginFunctions extends Connection{
    
    private $result;
    private $data;

    function __construct(){
        parent::__construct();
    }

    function SelectQuery($username){
        $q = "SELECT * FROM logins WHERE username = '$username';";
        $this->result = pg_query($this->dbconnection,$q);
    }

    function IsRegistered(){
        return pg_num_rows($this->result) >= 1 ? true : false;
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

    function PasswordMatch($input){
        return $this->data[1] == $input ? true : false;
    }

    function PageRedirect($status){
        return $status ? 'location : profile_management.php' : 'location : quote_history.php';
    }


}
?>