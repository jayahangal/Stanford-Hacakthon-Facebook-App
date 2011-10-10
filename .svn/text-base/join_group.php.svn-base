<?php
include_once 'facebook_include.php';
include_once 'mysqlconnect.php';

mysql_query("UPDATE users SET group_id = " . $_POST['group_id'] . " where user_id = " . $facebook->get_loggedin_user());

header('Location: http://network.faceaids.org/facebook/');
?>

