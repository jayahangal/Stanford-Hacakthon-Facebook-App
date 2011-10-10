<?php
include_once 'facebook_include.php';
echo "IN HERE";
include_once 'mysqlconnect.php';
mysql_query("UPDATE users SET points = points + 1 + " . count($_POST['id']) . " WHERE user_id = " . $facebook->get_loggedin_user());
header('Location: http://network.faceaids.org/facebook/');
?>