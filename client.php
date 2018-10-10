<?php

require_once "lib/nusoap.php";

$client = new nusoap_client("https://webservicehello.herokuapp.com/index.php?wsdl", true);

$data = $client->call("hello",array('name'=> 'Sawasdee'));

print_r($data);

?>
