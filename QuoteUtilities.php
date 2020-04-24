<?php
include_once('connection.php');

class QuoteFunctions extends Connection{
    
    private $quotes_query;
    private $address_query;
    private $address_array;
    private $delivery_date;
    
    private $total_price;
    private $suggested_price;
    

    private $gals;

    const CURRENT_PRICE_PER_GALLON = 1.50;
    const IN_STATE_RATE = 0.02;
    const OUT_STATE_RATE = 0.04;
    const HISTORY_RATE = 0.01;
    const NO_HISTORY_RATE = 0.00;
    const GALS_REQUESTED_FACTOR = 1000;
    const MORE_GALS_REQUESTED_RATE = 0.02;
    const LESS_GALS_REQUESTED_RATE = 0.03;
    const COMPANY_PROFIT_FACTOR = 0.10;
    const RATE_FLUCTUATION_SUMMER = 0.04;
    const RATE_FLUCTUATION_NOT_SUMMER = 0.03;



    function __construct(){
        parent::__construct();
    }

    #function ValidGallons($gals){
    #    $error_message = "";
    #    $gals_int = (int)$gals;
    #    
    #    if(preg_match('/\s/',$gals)){$error_message = "Gallons cannot contain a space!";}
    #    elseif(strpos($gal,'.') !== false){$error_message = "Gallons must be a whole number!";}
    #    elseif($gals_int <= 0){$error_message = "Gallons must be greater than 0!";}
    #    elseif(preg_match('/[^0-9\d]/',$gals)){ $error_message = "No special or alphabetic characters in gallons!"; }
    #    else{$this->gals = $gals_int;}
    #    return $error_message;
    #}

    function GetGallons($gals){
        $this->gals = $gals;
    }

    function ValidDate($date){
        date_default_timezone_set('America/Chicago');
        $error_message = "";
        if(strtotime($date) < strtotime(date("m/d/Y"))) { $error_message = "Delivery date cannot be set before today's date!";}
        else{$this->delivery_date = date("m/d/Y",strtotime($date));} 

        return $error_message;

    }

    function PullAddress($username){
        $q = "SELECT address1, address2, city, state, zipcode  FROM client_information WHERE username = '$username';";
        $this->address_query = pg_query($this->dbconnection,$q);
        return $this->address_query ? true : false;
    }

    function GetAddress(){
        $this->address_array = pg_fetch_row($this->address_query);
    }

    function PrintAddress(){

        if(!$this->address_array[1]){
            echo $this->address_array[0] . ", ";
            echo $this->address_array[2] . ", " . $this->address_array[3] . " " .  $this->address_array[4];
        }
        else{
            echo $this->address_array[0] . " " . $this->address_array[1] . ", ";
            echo $this->address_array[2] . ", " . $this->address_array[3] . " " .  $this->address_array[4];
        }
    }

    function PullQuotes($username){
        $q = "SELECT quotedate, 
        gallons, 
        deliveryaddress,
        deliverydate,
        suggestedprice,
        totalamount
        FROM fuel_quotes WHERE username = '$username' ORDER BY quotetimestamp DESC;";
        $this->quotes_query = pg_query($this->dbconnection,$q);
        return $this->quotes_query;
    }

    function PrintQuotes(){
        if($this->quotes_query){
            while($row = pg_fetch_array($this->quotes_query)):
                echo "<tr>";
					echo "<td align='center'>".$row[0]."</td>";
					echo "<td align='center'>".$row[1]."</td>";
					echo "<td align='center'>".$row[2]."</td>";
					echo "<td align='center'>".$row[3]."</td>";
					echo "<td align='center'>$".$row[4]."</td>";
					echo "<td align='center'>$".$row[5]."</td>";
				echo "</tr>";
            endwhile;
        }
    }


    function IsFirstQuote(){
        return pg_num_rows($this->quotes_query) >= 1 ? self::HISTORY_RATE : self::NO_HISTORY_RATE;
    }

    function IsInState(){
        return $this->address_array[3] == "TX" ? self::IN_STATE_RATE : self::OUT_STATE_RATE;
    }

    function IsSummer(){
        if(strtotime("06/20/2020") <= strtotime($this->delivery_date) && strtotime($this->delivery_date) <= strtotime("09/22/2020")){
            return self::RATE_FLUCTUATION_SUMMER;
        }
        else{
            return self::RATE_FLUCTUATION_NOT_SUMMER;
        }
    }

    function GallonsRequestedFactor(){
        return $this->gals > self::GALS_REQUESTED_FACTOR ? self::MORE_GALS_REQUESTED_RATE : self::LESS_GALS_REQUESTED_RATE;
    }

    function SuggestPrice(){
        $this->suggested_price = self::CURRENT_PRICE_PER_GALLON + $this->Margin();
        $this->suggested_price = number_format($this->suggested_price, 2, '.', '');
        return $this->suggested_price;
    }

    function IsSuggestedPriceSet(){
        return $this->suggested_price ? true : false;
    }

    function Margin(){
        $state_rate = $this->IsInState();
        $history_rate = $this->IsFirstQuote();
        $gals_rate = $this->GallonsRequestedFactor();
        $summer_rate = $this->IsSummer();
        /*
        echo "curr price per gallon= " . self::CURRENT_PRICE_PER_GALLON;
        echo "<br>";
        echo "State rate= ". $state_rate;
        echo "<br>";
        echo "Quote History rate= " . $history_rate;
        echo "<br>";
        echo "gallon threshhold rate= " . $gals_rate;
        echo "<br>";
        echo "company profit= " . self::COMPANY_PROFIT_FACTOR;
        echo "<br>";
        echo "summer rate= " . $summer_rate;
        */

        return self::CURRENT_PRICE_PER_GALLON * ($state_rate - $history_rate + $gals_rate + self::COMPANY_PROFIT_FACTOR + $summer_rate); 
    }


    function TotalPrice(){
        $this->total_price = $this->gals * $this->suggested_price;
        $this->total_price = number_format($this->total_price, 2, '.', '');
        return $this->total_price;
    }

    function ReturnDate(){
        return $this->delivery_date;
    }

    function SubmitQuote($user,$gallons,$deliv_date,$suggested_amount,$total_amount){
        date_default_timezone_set('America/Chicago');
        
        $address = $this->address_array[0] . " " . $this->address_array[1] . ", " . $this->address_array[2] . ", " . $this->address_array[3] . " " .  $this->address_array[4];
        
        $insert_array = ["username"=>$user,
        "quotedate"=>date("m/d/Y"),
        "gallons"=>(int)$gallons,
        "deliveryaddress"=>$address,
        "deliverydate"=>$deliv_date,
        "suggestedprice"=>(float)$suggested_amount,
        "totalamount"=>(float)$total_amount];

        /*
        foreach($insert_array as $key => $val){
            echo $key . " " . $val . " " . gettype($val) . "<br>";
        }
        */
        return pg_insert($this->dbconnection,"fuel_quotes",$insert_array);
        

    }   

}

?>
