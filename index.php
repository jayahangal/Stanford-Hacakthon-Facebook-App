<?php

require_once 'facebook_include.php';
require_once 'mysqlconnect.php';

echo "<br>";
echo "&nbsp;&nbsp;&nbsp;<a href='http://apps.facebook.com/face_aids/index.php'>News Feeds</a> | ";
echo "<a href='http://apps.facebook.com/face_aids/index.php?filter=st'>Status Feed</a> | ";
echo "<a href='http://apps.facebook.com/face_aids/index.php?filter=ev'>Events Feed </a>";

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

echo "<div style='overflow:hidden;width:530px;float:left;padding:25px 20px;'>";

if ($_GET['filter']=="st") {
	// Status Update Box
	echo "<fb:editor action='' method='POST'>
	    <fb:editor-textarea label='Status Update' value='' name ='$usergroup_id'/>
	    <fb:editor-buttonset>
	    <fb:editor-button value='Submit'/>
	    </fb:editor-buttonset>
	    </fb:editor><div style='float:left; height:20px; width:520px'></div>";

	$status = $_POST[$usergroup_id];
	if (isset($_POST[$usergroup_id]))  {
	        $facebook->require_login($required_permissions = 'status_update');
    	    $result = $facebook->api_client->Users_setStatus($_POST[$usergroup_id]);
	    $curr_time = date("Y-m-d H:i:s", time());
	    $query = "INSERT INTO status_updates VALUES('" . $curr_time . "', ' " . $status . " ', " . $usergroup_id . " , " . $profile . ")";
	    mysql_query($query);
	}

	// status output

	$sql = "SELECT * FROM status_updates ORDER BY creation_time desc LIMIT 0," . $num_of_rows_total;
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
	        echo '<div style=\'overflow:hidden;padding-bottom:20px;width:520px;border-bottom:1px solid lightgray;\'>'.
    	            '<div style=\'overflow:hidden;float:left;width:60px;\'>'.
        	        '<fb:profile-pic uid=\'' . $row['uid'] . '\' size=\'square\' />'.
            	    '</div><div style=\'overflow:hidden;float:left;margin-left:10px;width:450px;\'>' .
                	'<span style=\'font-weight:bold;font-size:13px\'>'.
	                '<fb:name uid=\'' . $row['uid'] . '\' /></span>&nbsp;&nbsp;' .
    	            $row['status'] . '<br><br><span style=\'color:gray\'>'.
        	        $row['creation_time'] . '</span></div></div>';
	}
} else if ($_GET['filter']=="ev") {
	$result = mysql_query("SELECT group_id FROM groups");
	while($row = mysql_fetch_array($result)) {
		$query =  "SELECT eid, name, tagline, pic, host, description,
			        event_type, event_subtype, start_time, end_time, creator, update_time,
			        location, venue
				 	FROM event
				 	WHERE eid IN (SELECT eid FROM event_member
     			  	WHERE uid = " . $row['group_id'] . ") AND
     			  	start_time > " . time();
		$sResult = $facebook->api_client->fql_query($query);
		if($sResult) {
			foreach($sResult as $sRow) {
				$img = $sRow['pic'];
				$eid = $sRow['eid'];
				$eName = $sRow['name'];
				$desc = $sRow['description'];
			
				echo '<div style="width:525px;overflow:hidden;padding-bottom:10px;border-bottom:1px solid gray">
					<div style="float:left;width:100px;padding:15px">';
					if($img)
						echo '<fb:tag name="img"><fb:tag-attribute name="src">'.$img.'</fb:tag-attribute></fb:tag>';
					else
						echo '<fb:tag name="img"><fb:tag-attribute name="src">http://network.faceaids.org/facebook/faceaids.jpg</fb:tag-attribute></fb:tag>';
					echo '</div>';
			
				echo '<div style="float:left;width:350px;padding:15px">
						<h2><a href = "http://www.facebook.com/event.php?eid=' . $eid . '">' . $eName . '</a></h2><br>'.
						$desc .'</div></div>';
			}
		}
	}
} else {
	// creates cache for status data

	$sql = "CREATE TEMPORARY TABLE cached_statuses ( " .
			"id INT(11) NOT NULL AUTO_INCREMENT, " .
			"created_time timestamp NOT NULL, " .
			"message mediumtext NOT NULL, ".
			"primary key(id))";
	mysql_query($sql) or die(mysql_error());

	$sql = "SELECT * FROM groups";
	$result = mysql_query($sql) or die(mysql_error());
	while ($row = mysql_fetch_array($result)) {
		$query = "SELECT message, actor_id, created_time FROM stream ".
				 "WHERE source_id = " . $row['group_id'];
		$stream = $facebook->api_client->fql_query($query);
	
		if ($stream) {	
			foreach ($stream as $status) {
				$sql = " INSERT INTO cached_statuses VALUES(NULL" .
						",'" . date("Y-m-d H:i:s", $status['created_time']) .
						"','<div style=\'overflow:hidden;padding-bottom:20px;width:520px;border-bottom:1px solid lightgray;\'>".
						"<div style=\'overflow:hidden;float:left;width:60px;\'>".
						"<fb:profile-pic uid=\'" . $status['actor_id'] . "\' size=\'square\' />".
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
}


// right column

echo "</div><div style = 'overflow:hidden;float:left;margin-top: 25px;'>";

// user picture and information
$result = mysql_query("SELECT group_name from groups where group_id = " . $usergroup_id);
$row = mysql_fetch_array($result);

echo "<fb:profile-pic size = 'normal' width='165px' uid = '" . $profile . "'/></div>".
	 "<div style = 'overflow:hidden; float:left; width:165px; padding:10px;'>" .
	 "<h2>Name: <fb:name uid='" . $profile . "' useyou='false'/></h2>" .
	 "<h2>Points: " . $points . "</h2>";

if($usergroup_id == "") 
	echo "<h2><a href = 'search_groups.php'>Choose a Chapter</a></h2>";
else
	echo "<h2>Chapter: <a href = 'http://www.facebook.com/group.php?gid=" . $usergroup_id . "'>" . $row['group_name'] . "</a></h2>";
echo "</div></div>";

// groups

$groups = "";
if(!is_null($usergroup_id)){
	$sql = "SELECT order_groups FROM groups WHERE group_id='".$usergroup_id."'";
	$result = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($result);
	$order_groups = explode(",", $row['order_groups']);
	$group_str = "(";
	for($i=0; $i<5; $i+=1){
		$curr_group = mt_rand(0, count($order_groups)-1);
		$picked_groups[$i] = $order_groups[$curr_group];
		array_splice($order_groups, $curr_group, 1);
	}
}

$sql = "SELECT * FROM groups";
$result = mysql_query($sql) or die(mysql_error());

while ($row = mysql_fetch_array($result)) {
	if(in_array($row['group_id'], $picked_groups)) {
		$groups .= "<div style='margin-left:10px;height:65px;width:60px;float:left;'>".
					"<fb:profile-pic uid='" . $row['group_id'] . "' size='square' /></div>".
					"<div style='height:45px;padding-top:20px;width:90px;float:left;'>" . 
					"<a href='group.php?group_id=" . $row['group_id'] . "'>". $row['group_name'] .
					"</a></div>";
	}
}

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
