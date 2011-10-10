<?php
require_once 'mysqlconnect.php';
include_once 'facebook_include.php';
include_once 'group_output.php';

$group_name = $_GET['group_name'];
$zip = $_GET['zip'];

echo "<br><h1>Search Results</h1><br><hr>";

$result = "";

if($group_name != "")
{
	$result = mysql_query("SELECT * FROM groups WHERE group_name like '%" . $group_name . "%'");
}
elseif($zip != "")
{
	$zip = substr($zip, 0, 3);
	$result = mysql_query("SELECT * FROM groups WHERE zip like '" . $zip . "%'");
}

while ($row = mysql_fetch_array($result)) 
{
	printGroupInfo($row['group_id'], $facebook);
	echo '<hr>';
	//echo "<h2><a href = 'http://www.facebook.com/group.php?gid=" . $row['group_id']. "'>" . $row['group_name'] . "</a></h2><br />";
}

?>