<?php
require 'connectdb.php';
// >= '2010-01-31 12:01:01'
$query = "SELECT * FROM data";

$result = mysqli_query($dbcon, $query);

if($result){
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo("id ".$row['roomid']."<br>");
        echo("time ".$row['time']."<br>");
        echo("temperature ".$row['temperature']."<br>");
        echo("humidity ".$row['humidity']."<br>");
        // echo "fan ".$row['fan']."<br>";
        echo "<br>";
    }
    mysqli_free_result($result);
}else{
    echo "error";
}

mysqli_close($dbcon);
?>
    