<?php
require 'lib/nusoap.php';

$server = new nusoap_server(); // Create a instance for nusoap server
$server->configureWSDL("Soap Demo","urn:soapdemo"); // Configure WSDL file

// function hello
function hello($name) {
		return "Hello, $name";
	}


$server->register("hello", // function hello
    array("name" => "xsd:string"), // input type string
    array("return" => "xsd:string"));  // output type string

// Run service
$server->service(file_get_contents("php://input"));

?>

