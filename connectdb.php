<?php 
	$dbcon =  mysqli_connect('us-cdbr-iron-east-01.cleardb.net', 'b527b3315d2375', '50a5650c', 'heroku_412cbb6c0f293a3') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');	

?>