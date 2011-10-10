<?php
include_once 'facebook_include.php';
include_once 'mysqlconnect.php';

echo "<br><h1>Upcoming Events</h1><br><hr>";

$result = mysql_query("SELECT group_id FROM groups");

while($row = mysql_fetch_array($result))
{
	$query =  "
	SELECT eid, name, tagline, pic, host, description,
        event_type, event_subtype, start_time, end_time, creator, update_time,
        location, venue
 	FROM event
 	WHERE eid IN (SELECT eid FROM event_member
     			  	WHERE uid = " . $row['group_id'] . ") AND
     			  	start_time > " . time();
	
	
	$sResult = $facebook->api_client->fql_query($query);
	if($sResult)
	{
		foreach($sResult as $sRow)
		{
			$img = $sRow['pic'];
			$eid = $sRow['eid'];
			$eName = $sRow['name'];
			$desc = $sRow['description'];
			
			echo '<div style="overflow:hidden; width:800px">
				<div style="float:left; width:250px; padding:15px">';
				if($img)
					echo '<fb:tag name="img"><fb:tag-attribute name="src">'.$img.'</fb:tag-attribute></fb:tag>';
				else
					echo '<fb:tag name="img"><fb:tag-attribute name="src">http://network.faceaids.org/facebook/faceaids.jpg</fb:tag-attribute></fb:tag>';
				echo '</div>';
			
			echo '<div style="float:left; width:300px; padding:15px">
					<h2><a href = "http://www.facebook.com/event.php?eid=' . $eid . '">' . $eName . '</a></h2><br>'.
					$desc .'
				 </div></div><hr>';
					
			
		}
	}
}

?>
