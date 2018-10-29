<?php
// Pull in the NuSOAP code
require_once('lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('maildata', 'urn:maildata');

// Register the data structures used by the service
$server->wsdl->addComplexType(
    'MailData',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'addr' => array('name' => 'addr', 'type' => 'xsd:string'),
        'weight' => array('name' => 'weight', 'type' => 'xsd:float')
    )
);

$server->wsdl->addComplexType(
    'SentMail',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id' => array('name' => 'id', 'type' => 'xsd:integer'),
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'addr' => array('name' => 'addr', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'MailStatus',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'id' => array('name' => 'id', 'type' => 'xsd:integer'),
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'addr' => array('name' => 'addr', 'type' => 'xsd:string'),
        'weight' => array('name' => 'weight', 'type' => 'xsd:float'),
        'status' => array('name' => 'status', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'GetMail',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'GetMail' => array('name' => 'GetMail','minOccurs'=> '0', 'maxOccurs' =>'unbounded','nillable' => 'true', type=>'tns:MailStatus')
    )
);

//Define the method as a PHP function
function send_mail($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $name = $data['name'];
    $addr = $data['addr'];
    $weight = $data['weight'];

    $query = "INSERT INTO maildata (id, name, addr, weight, status) VALUES('','$name','$addr','$weight', 'false')";
    $result = mysqli_query($dbcon, $query);
    mysqli_close($dbcon);
    $send = "add data complete!";
    return $send;
}

//Register the method to expose
$server->register('send_mail',           // method name
    array('data' => 'tns:MailData'),     // input parameters
    array('return' => 'xsd:string'),    // output parameters
                    'urn:maildata');

function sent_mail_already($data) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $id = $data['id'];
    $name = $data['name'];
    $addr = $data['addr'];
    $weight = $data['weight'];
    $query = "SELECT * FROM maildata WHERE id = '$id' and name = '$name' and addr = '$addr'";
    $result = mysqli_query($dbcon, $query);
    if($result){
       $query = "UPDATE maildata SET status = 'true' WHERE id = '$id'and name = '$name' and addr = '$addr'";
       $result = mysqli_query($dbcon, $query);
        }
    
    mysqli_close($dbcon);
   return "sent mail already and status updated";
}

//Register the method to expose
$server->register('sent_mail_already',           // method name
    array('data' => 'tns:SentMail'),
    array('return' => 'xsd:string'),    // output parameters
                        'urn:maildata');         

function get_info_all($anything) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $query = "SELECT * FROM maildata";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = array('id'=>$row['id'], 'name'=>$row['name'], 'addr'=>$row['addr'], 'weight'=>$row['weight'], 'status'=>$row['status']);
        }
    }
    mysqli_close($dbcon);
    return array('GetMail' => $data);
}

//Register the method to expose
$server->register('get_info_all',           // method name
    array('anything' => 'xsd:string'),
    array('return' => 'tns:GetMail'),    // output parameters
                        'urn:maildata');         
// 

function get_info_from_status($status) {
    $dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
    mysqli_set_charset($dbcon, 'utf8');
    $query = "SELECT * FROM maildata WHERE status = '$status'";
    $result = mysqli_query($dbcon, $query);
    if($result){
        $data = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = array('id'=>$row['id'], 'name'=>$row['name'], 'addr'=>$row['addr'], 'weight'=>$row['weight'], 'status'=>$row['status']);
        }
    }
    
    mysqli_close($dbcon);
    return array('GetMail' => $data);
}

// Register the method to expose
$server->register('get_info_from_status',           // method name
    array('status' => 'xsd:string'),
    array('return' => 'tns:GetMail'),    // output parameters
                        'urn:maildata');         


// Use the request to (try to) invoke the service
@$server->service(file_get_contents("php://input"));
?>