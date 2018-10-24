<?php
// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('airdata', 'urn:airdata');

// Register the data structures used by the service
$server->wsdl->addComplexType(
    'Air_Data',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'roomid' => array('name' => 'roomid', 'type' => 'xsd:integer'),
        'time' => array('name' => 'time', 'type' => 'xsd:string'),
        'temperature' => array('name' => 'temperature', 'type' => 'xsd:float'),
        'humidity' => array('name' => 'humidity', 'type' => 'xsd:float'),
    )
);

$server->wsdl->addComplexType(
    'GetAir',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'GetAir' => array('name' => 'GetAir','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:AirData')
    )
);


// Define the method as a PHP function
function set_data($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $roomid = $data['roomid'];
    $time = $data['time'];
    $temperature = $data['temperature'];
    $humidity = $data['humidity'];
    $query = "INSERT INTO data_table(roomid, time, temperature, humidity) VALUES('$room','$time','$temperature','$humidity')";
    // $query = "INSERT INTO data_table(room, time, temp, humidity) VALUES('01', '12-09-2016 05:00', '22.5', '10.2')";
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}

// Register the method to expose
$server->register('set_data',                    // method name
    array('data' => 'tns:AirData'),          // input parameters
    array('return' => 'xsd:string'),    // output parameters
    'urn:airdata',                         // namespace
    'urn:airdata#set_data'                   // soapaction
);

function get_data($room) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $query = "SELECT * FROM data";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = array('roomid'=>$row['roomid'], 'time'=>$row['time'], 'temperature'=>$row['temperature'], 'humidity'=>$row['humidity']);
        }
    }
    
    mysqli_close($dbcon);
    return array('GetAir' => $data);
}

// Register the method to expose
$server->register('get_data',                    // method name
    array('room' => 'xsd:string'),
    array('return' => 'tns:GetAir'),    // output parameters
                        'urn:airdata',                         // namespace
                        'urn:airdata#get_data');                  // soapaction


// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>