<?php

require_once 'facebook_include.php';
require_once 'mysqlconnect.php';

//Checks if the user is in database and aggreagates points
$profile = $facebook->get_loggedin_user();
$result = mysql_query("SELECT  * FROM users WHERE user_id = '" . $profile . "'");

$points = 0;
$usergroup_id = "";
$row;
if(mysql_num_rows($result) == 0)
{
	$points = 1;
	mysql_query("INSERT INTO users (user_id, points) VALUES (" . $profile . ", 1)");
	$result = mysql_query("SELECT  * FROM users WHERE user_id = '" . $profile . "'");
	$row = mysql_fetch_array($result);
}
else
{
	$row = mysql_fetch_array($result);	
	$points = $row['points']; 
	
	if (strtotime(date('Y-m-d H:i:s')) - strtotime($row['last_access']) > 86400)
	{
		$points = $points + 1;
		mysql_query("UPDATE users SET points = " . $points . " WHERE user_id = " . $profile);	
	}	
}
$usergroup_id = $row['group_id'];

$facebook->require_login($required_permissions = 'read_stream');

$num_of_rows_per_group = 5;
$num_of_rows_total = 20;

// Status Update Box
echo "
    <fb:editor action='' method='POST'>
    <fb:editor-text label='Status Update' value='' name ='$usergroup_id'/>
    <fb:editor-buttonset>
    <fb:editor-button value='Submit'/>
    </fb:editor-buttonset>
    </fb:editor>";

$status = $_POST[$usergroup_id];
/*if (isset($_POST[$usergroup_id]))  {
        $facebook->require_login($required_permissions = 'status_update');
        $result = $facebook->api_client->Users_setStatus($_POST[$usergroup_id]);
}*/

$curr_time = date("Y-m-d H:i:s", time());
$query = "INSERT INTO status_updates VALUES('" . $curr_time . "', ' " . $status . " ', " . $usergroup_id . " , " . $profile . ")";
mysql_query($query);
echo "$status";

// status output
$sql = "SELECT * FROM status_updates ORDER BY creation_time desc LIMIT 6," . $num_of_rows_total;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($result)) {
        echo  $row['status'];
	echo "<br>";
        echo  $row['creation_time'];
        echo "<br><br>";
}

echo "<div style='overflow:hidden;width:530px;float:left;padding:25px 20px;'>";

// creates cache for status data

$sql = "CREATE TEMPORARY TABLE cached_statuses ( " .
		"id INT(11) NOT NULL AUTO_INCREMENT, " .
		"created_time timestamp NOT NULL, " .
		"message mediumtext NOT NULL, ".
		"primary key(id))";
mysql_query($sql) or die(mysql_error());

$groups = "";
$sql = "SELECT * FROM groups";
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($result)) {
	$query = "SELECT message, actor_id, created_time FROM stream ".
			 "WHERE source_id = " . $row['group_id'];
	$stream = $facebook->api_client->fql_query($query);
	
	if ($stream) {	
		$groups .= "<div style='margin-left:10px;height:65px;width:60px;float:left;'>".
					"<fb:profile-pic uid='" . $row['group_id'] . "' size='square' /></div>".
					"<div style='height:45px;padding-top:20px;width:90px;float:left;'>" . 
					"<a href='group.php?group_id=" . $row['group_id'] . "'>". $row['group_name'] .
					"</a></div>";
		foreach ($stream as $status) {
			$sql = " INSERT INTO cached_statuses VALUES(NULL" .
					",'" . date("Y-m-d H:i:s", $status['created_time']) .
					"','<div style=\'overflow:hidden;padding-bottom:20px;width:520px;border-bottom:1px solid lightgray;\'>".
					"<div style=\'overflow:hidden;float:left;width:60px;\'>".
					"<fb:profile-pic uid=\'" . $row['group_id'] . "\' size=\'square\' />".
					"</div><div style=\'overflow:hidden;float:left;margin-left:10px;width:450px;\'>" .
					"<span style=\'font-weight:bold;font-size:13px\'>".
					"<a href=\'http://www.facebook.com/group.php?gid=" . $row['group_id'] . "\'>".
					$row['group_name'] . "</a></span>&nbsp;&nbsp;" .
					addslashes($status['message']) . "<br><br><span style=\'color:gray\'>".
					"Posted by <fb:name uid=\'" . 
					$status['actor_id'] . "\' />')";
			mysql_query($sql) or die(mysql_error()."<br>".$sql);
		}
	}
}

// output

$sql = "SELECT * FROM cached_statuses ORDER BY created_time desc LIMIT 0," . $num_of_rows_total;
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($result)) {
		echo stripslashes($row['message']) . $row['authors'] . " (" . 
			 $row['created_time'] . ")</span></div></div><br><br>";
}

$sql = "DROP TABLE cached_statuses";
mysql_query($sql) or die(mysql_error());

// right column

echo "</div><div style = 'overflow:hidden;float:left;margin-top: 25px;'>";

// user picture and information
$result = mysql_query("SELECT group_name from groups where group_id = " . $usergroup_id);
$row = mysql_fetch_array($result);

echo "<fb:profile-pic size = 'normal' width='165px' uid = '" . $profile . "'/></div>".
	 "<div style = 'overflow:hidden; float:left; padding:10px;'>" .
	 "<h2>Name: <fb:name uid='" . $profile . "' useyou='false'/></h2>" .
	 "<h2>Points: " . $points . "</h2>";

if($usergroup_id == "") 
	echo "<h2><a href = 'search_groups.php'>Choose a Chapter</a></h2>";
else
	echo "<h2>Chapter: <a href = 'http://www.facebook.com/group.php?gid=" . $usergroup_id . "'>" . $row['group_name'] . "</a></h2>";
echo "</div></div>";

// groups

echo "<div style='overflow:hidden;float:left;margin-top:10px;border:1px solid #94A3C4;width:165px;border-right:0px'>";
echo "<div style='font-weight:bold;font-size:13px;width:155px;margin-bottom:10px;padding:5px 10px;background-color:#ECEFF5'>Groups</div>" . $groups;
echo "</div>";

// top user points

$users = "";
$sql = "SELECT * FROM users ORDER BY points desc LIMIT 0,10";
$result = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($result)) {
	$users .= "<div style='margin-left:10px;height:65px;width:60px;float:left;'>".
				"<fb:profile-pic uid='" . $row['user_id'] . "' size='square' /></div>".
				"<div style='height:55px;padding-top:10px;width:90px;float:left;'>" . 
				"<fb:name uid='" . $row['user_id'] . "' useyou='false'/><br>" .
				"<b>" . $row['points']. "</b> points</div>";
}

echo "<div style='overflow:hidden;float:left;margin-top:20px;border:1px solid #94A3C4;width:165px;border-right:0px'>";
echo "<div style='font-weight:bold;font-size:13px;width:155px;margin-bottom:10px;padding:5px 10px;background-color:#ECEFF5'>Top Users</div>" . $users;
echo "</div>";
