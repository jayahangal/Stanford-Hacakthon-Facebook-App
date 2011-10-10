<?php
include_once 'facebook_include.php';
include_once 'admin_include.php';

if ($_POST['super_admin']) {
	$result = mysql_query("SELECT * FROM users WHERE user_id = " . $_POST['super_admin']) or die(mysql_error());
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		if ($row['access_lvl']==2) {
			echo "<br><fb:error message='" . $_POST['super_admin_uid'] . 
		         " is already a super administrator' />";
		} else {
			$sql = "UPDATE users SET access_lvl = 2 WHERE user_id = " . $_POST['super_admin'];
			mysql_query($sql) or die(mysql_error());
			echo '<br><fb:success message="' . $_POST['super_admin_uid']. ' invited to be a super administrator." />';
		}
	} else {
		$sql = "INSERT INTO users VALUES('" . $_POST['super_admin'] . "',2,0,0,0,'0000-00-00 00:00:00')";
		mysql_query($sql) or die(mysql_error());
		echo '<br><fb:success message="' . $_POST['super_admin_uid']. ' invited to be a super administrator." />';
	}
}

if ($_POST['admin']) {
	$admin_group_id = 0;
	$sql = "SELECT * FROM users WHERE user_id=" . $profile;
	mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($result);
	if ($access_level == 1) $admin_group_id = $row['admin_group_id'];
	
	$result = mysql_query("SELECT * FROM users WHERE user_id = " . $_POST['admin']) or die(mysql_error());
	if (mysql_num_rows($result)) {
		$row = mysql_fetch_array($result);
		if ($row['access_lvl']) {
			echo "<br><fb:error message='" . $_POST['admin_uid'] . 
		         " is already an administrator' />";
		} else {
			$sql = "UPDATE users SET access_lvl = 1, group_id = $admin_group_id, ".
					"admin_group_id=$admin_group_id WHERE user_id = " . $_POST['admin'];
			mysql_query($sql) or die(mysql_error());
			echo '<br><fb:success message="' . $_POST['admin_uid']. ' granted administrator permissions. Invite to FACE AIDS." />';
		}
	} else {
		$sql = "INSERT INTO users VALUES('" . $_POST['admin'] . "',1,$admin_group_id,".
				"$admin_group_id,0,'0000-00-00 00:00:00')";
		mysql_query($sql) or die(mysql_error());
		echo '<br><fb:success message="' . $_POST['admin_uid'].
			' granted administrator permissions. Invite to FACE AIDS." />';
	}
}

if ($access_level == 2) {
	echo '<span style="color:gray;font-weight:bold;">
			<form method="post" action="admin.php"><br>
			<table cellspacing=10px style="margin-left:200px;">
			<tr>
				<td align="right">Add Admin </td>
				<td><fb:friend-selector uid="$profile" name="admin_uid" idname="admin" /></td>
			</tr>
			<tr>
				<td align="right">Add Super Admin </td>
				<td><fb:friend-selector uid="$profile" name="super_admin_uid" idname="super_admin" /></td>
			</tr>
			<tr><td></td>
			<td><input type="submit" value="Grant permission" name="submit"/></td>
			</tr>
			</table>
			</form></span>';
} else {
	echo '<span style="color:gray;font-weight:bold;">
			<form method="post" action="admin.php"><br>
			<table cellspacing=10px>
			<tr>
				<td align="right">Add Admin </td>
				<td><fb:friend-selector uid="$profile" name="uid" idname="admin" /></td>
			</tr>
			<tr><td></td>
			<td><input type="submit" value="Grant permission" name="submit"/></td>
			</tr>
			</table>
			</form>';
}

?>