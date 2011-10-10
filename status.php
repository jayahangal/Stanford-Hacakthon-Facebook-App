<?php

require_once 'facebook_include.php';
require_once 'mysqlconnect.php';

//Checks if the user is in database and aggreagates points
$profile = $facebook->get_loggedin_user();
$result = mysql_query("SELECT  * FROM users WHERE user_id = '" . $profile . "'");

$points = 0;
$usergroup_id;
$table = "status_updates";
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

        echo "
        <fb:editor action='' method='POST'>
        <fb:editor-text label='Status Update' value='' name ='$usergroup_id'/>
         <fb:editor-buttonset>
           <fb:editor-button value='Submit'/>
         </fb:editor-buttonset>
        </fb:editor>";

	$status = $_POST[$usergroup_id]; 
/*if (isset($_POST[$usergroup_id]))  {
	echo "$_POST[$usergroup_id]"; 
	$facebook->require_login($required_permissions = 'status_update');
	$result = $facebook->api_client->Users_setStatus($_POST[$usergroup_id]);
}*/

       $curr_time = date("Y-m-d H:i:s", time());
	echo $curr_time;
       $query = "INSERT INTO status_updates VALUES('" . $curr_time . "', ' " . $status . " ', " . $usergroup_id . " , " . $profile . ")";
       mysql_query($query);
       mysql_close();	
	echo "$status"; 
