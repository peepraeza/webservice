<?php

require_once "lib/nusoap.php";

$client = new nusoap_client("http://localhost/soap/service.php?wsdl", true);

$data = $client->call("hello",array('name'=> 'Peerawit'));

print_r($data);

?>
