

<?php

class Connection{
    public $dbconnection;
   
    function __construct(){
        $this->dbconnection = pg_connect("host=localhost dbname=postgres user=postgres password=brownCow01") or die("Error connecting to database");
    }

    function Disconnect(){
        pg_close($this->dbconnection);
    }

    function IsConnected(){
        return $this->dbconnection ? true : false;
    }

    function GetConnection(){
        return $this->dbconnection;
    }

}


?>