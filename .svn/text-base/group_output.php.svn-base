<?php
include_once 'facebook_include.php';
include_once 'mysqlconnect.php';
	
function printGroupInfo($group_id,$facebook) {
	$group = $facebook->api_client->groups_get("", $group_id);
	
	$query = "select * from groups where group_id = ". $group_id;
	$result = mysql_query($query);
	$rows = mysql_num_rows($result);
	
	if ($group && $rows) {
	$gid = $group[0]['gid'];
	$img = $group[0]['pic_big'];
	//$group_members = $facebook->api_client->groups_getMembers($gid);
	$members = $group_members['members'];
	
	$query = "SELECT uid FROM group_member WHERE gid=".$gid;
    $num_members = count($facebook->api_client->fql_query($query));
	//$num_members = count($members);
	if ($num_members>=495)
		$num_members="more than 500";	
		
	$admins = $group_members['admins'];
	
	$row = mysql_fetch_row($result);
	
	echo '<div style="overflow:hidden">
		<div style="float:left; width:250px; padding:15px">
			<fb:tag name="img"><fb:tag-attribute name="src">'.$img.'</fb:tag-attribute></fb:tag>
		</div>';
	
	echo '<div style="float:left; width:300px; padding:15px">
			<h2><a href = "http://www.facebook.com/group.php?gid=' . $row[0]. '">' . $row[6] . '</a></h2><br>'.
			$group[0]['description'].'
			<div style= "float:left; width:100px; padding:15px"><fb:editor action="join_group.php" method="POST">
 	 	  	<fb:editor-custom>
 	 	  		<input type = "hidden" name = "group_id" value = "' . $group_id . '"/>
 	 	  	</fb:editor-custom>
			<fb:editor-buttonset>
 	   			<fb:editor-button value="Join Chapter"/>
 	 		</fb:editor-buttonset>
		  </fb:editor></div>
		</div>';
	
	echo '<div style="float:left; width:100px; padding:15px">
		 <h3>Group Info: </h3><br>
			Admins: ';
	
	if ($admins) {
		foreach ($admins as $uid) {
			$fields = array('first_name','last_name');
			$users = $facebook->api_client->users_getInfo($uid,$fields);
			if ($uid)
				echo '<br><fb:userlink uid="'.$uid.'"/>';
		}
	} else {
		echo '<br>None';
	}
			
	echo '<br><br>Members: '.$num_members.'<br><br>
		Location: '.$row[1].', '.$row[2].'
	</div></div>';
	
	}
	else {
		echo $group_id.": Not a Face AIDS group";
	}
	
}
?>