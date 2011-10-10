<?php

  require_once 'facebook.php';
  include "mysqlconnect.php";
  include "update_distances.php";
  include "google_maps.php";

  $api = "064cf9195ceddfff37f8d44dde422fb1";
  $secret = "83fc78ff5ab6fa6058d77fb570e14bf7";
  $facebook = new Facebook($api,$secret);
  $profile = $facebook->get_loggedin_user();

  if(!is_null($_GET['default_location'])){
     mysql_query("UPDATE users SET group_id='".$_GET['default_location']."' WHERE user_id='".$profile."'");
  }

  $res = mysql_query("SELECT * FROM groups")
         or die ("Unable to run query");
  $user_res = mysql_query("SELECT * FROM users WHERE user_id='".$profile."'");

  $default_loc = 0;
  if(mysql_num_rows($user_res) != 0){
    $user_row = mysql_fetch_array($user_res);
    $default_loc = $user_row['group_id'];
  }
?>
<script src="jquery.js" type="text/javascript" language="javascript"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAxGlW3vR-ctmoXiK_Qx655hRI6c91rUMYWqolaCInUnbo8XfAyBSnngw-xshy_eCpJBTbF4GHzKzAGQ" type="text/javascript"></script>    <script type="text/javascript"> 

    function initialize() {
	if (GBrowserIsCompatible()) {
		var map = new GMap2(document.getElementById("map_canvas"));
		map.setCenter(new GLatLng(37.0902400, -95.7128910), 3);
		map.setUIToDefault();

		// Create a base icon for all of our markers that specifies the
		// shadow, icon dimensions, etc.
		var baseIcon = new GIcon(G_DEFAULT_ICON);
		baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
		baseIcon.iconSize = new GSize(20, 34);
		baseIcon.shadowSize = new GSize(37, 34);
		baseIcon.iconAnchor = new GPoint(9, 34);
		baseIcon.infoWindowAnchor = new GPoint(9, 2);

		// Creates a marker whose info window displays the letter corresponding
		// to the given index.
		function createMarker(point, marker_string) { 
			var marker = new GMarker(point);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(marker_string);
		});
		return marker;
	}        
 
<?php
	while($row = @mysql_fetch_assoc($res)){
		$id = $row["group_id"];
		$lat = $row['latitude'];
		$lng = $row['longitude'];
		$group_name = $row["group_name"];
		$query = "SELECT '' FROM group_member WHERE gid=".$id;
		$num_members = count($facebook->api_client->fql_query($query));
		$marker_text = "<b><a class=\"group_name\" href=\"http://www.facebook.com/group.php?gid=".$id."\" target=_parent>".$group_name."</a></b><p>";
		$marker_text = $marker_text."Number of members: ".$num_members."<br>";
		$script_name = $_SERVER['PHP_SELF'];

		if($default_loc == $id){
			$marker_text = $marker_text."<b>Default chapter</b>";
			echo "map.zoomIn();\n";
			echo "map.zoomIn();\n";
			echo "map.zoomIn();\n";
			echo "map.panTo(new GLatLng($lat, $lng));\n";
		}else{
			$marker_text = $marker_text."<a href=\"".$script_name."?default_location=".$id."\">Make this my default chapter</a>";
		}
		echo "map.addOverlay(createMarker(new GLatLng($lat, $lng), '$marker_text'));\n";
		//var_dump($xml);
		$query = sprintf("UPDATE groups " .
				 " SET latitude = '%s', longitude = '%s' " .
				 " WHERE group_id = '%s' LIMIT 1;",
				 mysql_real_escape_string($lat),
				 mysql_real_escape_string($lng),
				 mysql_real_escape_string($id));
		$update_result = mysql_query($query);
		if (!$update_result) {
			die("Invalid query: " . mysql_error());
		}
	}

?> 
      }
    }
    </script> 
	
<style type="text/css">
body {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:11px;
}
.top {
margin-bottom: 15px;
}
.messagebox{
	position:absolute;
	width:100px;
	margin-left:30px;
	border:1px solid #c93;
	background:#ffc;
	padding:3px;
}
.messageboxok{
	position:absolute;
	width:auto;
	margin-left:30px;
	border:1px solid #349534;
	background:#C9FFCA;
	padding:3px;
	font-weight:bold;
	color:#008000;
	
}
.messageboxerror{
	position:absolute;
	width:auto;
	margin-left:30px;
	border:1px solid #CC0000;
	background:#F7CBCA;
	padding:3px;
	font-weight:bold;
	color:#CC0000;
}

.group_name{
    font-weight:bold;
    text-decoration:none;
    color:#3B5998;
    font-size:14px;
}

.group_name:hover{
    text-decoration:underline;
}
</style>

  </head> 
  <body onload="initialize()" onunload="GUnload()"> 
	<h2>Face AIDS Chapters:</h2>
    <div id="map_canvas" style="width: 500px; height: 300px"></div>