<?php
require_once "lib/nusoap.php";
// require "connectdb.php";
$us = "urn:powerdata";
$server = new soap_server();
$server->configureWSDL('PowerData', $us);
$server->wsdl->schemaTargerNamespace->$us;
$server->soap_defencoding = 'utf-8';

if(!isset($HTTP_RAW_POST_DATA)){
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}

function getInfoAircon($lan){
	$dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');	
    $query = "SELECT * FROM data";
    $result = mysqli_query($dbcon, $query);
    
    if($result != null){
        $xml .= "<information>";
        if($result){
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		        $xml .="<data>";
    			$xml .= "<roomid>".$row['roomid']."</roomid>";
    			$xml .= "<time>".$row['time']."</time>";
    			$xml .= "<temperature>".$row['temperature']."</temperature>";
    			$xml .= "<humidity>".$row['humidity']."</humidity>";
    			$xml .="</data>";
		    }
        }else{
            $xml .= "<error>No Data</error>";
        }
        $xml .="</information>";
    }else{
        $xml .="<error>Error".mysqli_error()."</error>";
    }
    $response = new soapval('return', 'xsd:string', $xml);
    mysqli_close($dbcon);
    return $response;
}
$server->register("getInfoAircon", array("getdata"=>"xsd:string"),
				 				array("return"=>"xsd:string"), $us);


function insertAirData($roomid, $time, $temperature, $humidity){
	$dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');	
    $insert = "INSERT INTO data (roomid, time, temperature, humidity) VALUES ('$roomid', '$time', '$temperature', '$humidity')";
	$result = mysqli_query($dbcon, $insert);

    return "data inserted";
}

$server->register("insertAirData", array("roomid" => "xsd:integer",
                                         "time" => "xsd:string",
                                         "temperature" => "xsd:float",
                                         "humidity" => "xsd:float"),
                                    array("return"=> "xsd:string"), $us);


$server->service($HTTP_RAW_POST_DATA);