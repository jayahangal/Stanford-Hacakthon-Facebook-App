<?php
include_once 'facebook.php';
include_once 'mysqlconnect.php';
$api = "064cf9195ceddfff37f8d44dde422fb1";
$secret = "83fc78ff5ab6fa6058d77fb570e14bf7";
$facebook = new Facebook($api,$secret);

$facebook->require_login($required_permissions = 'read_stream');

$profile = $facebook->get_loggedin_user();
$result = mysql_query("SELECT  * FROM users WHERE user_id = '" . $profile . "'");
$access_level = 0;

if(mysql_num_rows($result) != 0)
{
	$row = mysql_fetch_array($result);
	$access_level = $row['access_lvl'];
}

echo '<fb:dashboard></fb:dashboard>';

echo '<fb:tabs>
	<fb:tab-item href="index.php" title="Home" />';
	
	if ($access_level > 1 || ($access_lvl == 1 && $row['admin_group_id'])) {
		echo '<fb:tab-item href="admin.php" title="Add Admin" />';
	}
	echo '<fb:tab-item href="search_groups.php" title="Search Chapters" />';
	if ($access_level == 1 && !$row['admin_group_id']) {
		echo '<fb:tab-item href="register_group.php" title="Register Chapter" />';
	} elseif ($access_level == 1 && !$row['admin_group_id']==NULL) {
		echo '<fb:tab-item href="register_group.php" title="Edit Chapter" />';
	} elseif ($access_level == 2) {
		echo '<fb:tab-item href="register_group.php" title="Edit/Register Chapter" />';
	}
	echo '<fb:tab-item href="maps2.php" title="Chapter Map" />
	<fb:tab-item href="http://www.faceaids.org/donate" title="Make Donation" />
	<fb:tab-item href="send_requests.php" title="Invite Friends" />
</fb:tabs>';

?>