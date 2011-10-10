<?php
	include_once 'facebook_include.php';
	include_once 'mysqlconnect.php';
	
	$group_name = $_POST["group_name"];
	$group_id = $_POST["group_id"];
	$address = $_POST["address"];
	$zip = $_POST["zip"];
	
	$check_query = "select * from groups where group_id = " . $group_id;
	$rows = mysql_num_rows($check_query);
	
	if ($rows==0) {
		header('Location: http://network.faceaids.org/facebook/register_group.php');
	} else {
		$query = "INSERT INTO groups (group_name, group_id, street_address, zip) VALUES ('" . $group_name . "', " . $group_id . ", '" . $address . "', " . $zip . ")";
		mysql_query($query);
		mysql_close();
	
		header('Location: http://network.faceaids.org/facebook/group.php?group_id=' . $group_id);
	}
?>
