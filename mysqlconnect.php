<?php	
$user = "stanford_dm";
$password = "hackathon";
$host = "mysql.faceaids.org";
$database = "faceaids_network";
$link = mysql_connect($host,$user,$password);

mysql_select_db($database) or die( "Unable to select database");
?>