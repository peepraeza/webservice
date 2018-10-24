<?php 
	$dbcon =  mysqli_connect('us-cdbr-iron-east-04.cleardb.net', 'b29063783735c9', '55796c19', 'heroku_9a687a4905ad805') or die('not connect database'.mysqli_connect_error());
	mysqli_set_charset($dbcon, 'utf8');