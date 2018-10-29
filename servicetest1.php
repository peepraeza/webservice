<?php

require_once('lib/nusoap.php');
$server = new soap_server();
$server->configureWSDL('studentdata', 'urn:studentdata');

// Register the data structures used by the service
$server->wsdl->addComplexType(
    'StudentData',
    'complexType',
    'struct',
    'sequence',
    '',
    array('stdname' => array('name' => 'stdname', 'type' => 'xsd:string'),
          'stdnumber' => array('name' => 'stdnumber', 'type' => 'xsd:string'),
          'stdhobby' => array('name' => 'stdhobby', 'type' => 'tns:Hobby')
    )
);

$server->wsdl->addComplexType(
 'Hobby',
 'complexType',
 'array',
 '',
 'SOAP-ENC:Array',
  array(),
  array(
    array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:string[]')
  ),
  'xsd:string'
);

$server->wsdl->addComplexType(
    'GetStudentData',
    'complexType',
    'struct',
    'sequence',
    '',
    array('GetStudentData' => array('name' => 'GetStudentData','minOccurs'=> '0', 'maxOccurs' =>'unbounded',
                                  'nillable' => 'true', 'type'=>'tns:StudentData')
    )
);

function get_std_data($room) {
    $data[] = array("stdname"=> "Peerawit Wandech", "stdnumber" => "5801012620054", "stdhobby" => array('Play tabletennis', 'Basketball'));
    return array('GetStudentData' => $data);
}

// Register the method to expose
$server->register('get_std_data',           // method name
    array('data' => 'xsd:string'),              // input parameter
    array('return' => 'tns:GetStudentData'),    // output parameters
                      'urn:studentdata');         

@$server->service(file_get_contents("php://input"));
?>