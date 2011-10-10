<link type="text/css" rel="stylesheet" href="ajax_style.css" /> 

<?php
require_once 'facebook.php';
include_once 'facebook_include.php';
include_once 'mysqlconnect.php';
include_once 'admin_include.php';
include_once 'address_checker.php';
include_once 'update_distances.php';

$invalid_address = "<div class = 'messageboxerror'><br>Invalid Address: Address not found or too many matches. Please enter exact address.<br></div>";
$invalid_group_string = "<br>Invalid group id: group either doesn't exist or is already registered.<br>";

$group_name = $_POST["group_name"];
$group_id = $_POST["group_id"];
$address = $_POST["street_address"];
$zip = $_POST["zip"];
$selected = $_POST["selected"];

$register = $_POST["register"];
//echo $register;
$edit = $_POST["edit"];
//echo $edit;

$result = mysql_query("SELECT  * FROM users WHERE user_id = '" . $profile . "'");
$row = mysql_fetch_array($result);
$admin_gid = $row['admin_group_id'];

$values=array('group_id'=>"",'group_name'=>"",'street_address'=>"",'zip'=>"");
$errors = array("group_name"=>"","group_id"=>"","street_address"=>"","zip"=>"");

function displayForm($values,$errors,$action){
		if($action=="register"){
			echo "<br><h2>Register a new chapter:</h2>";
		}else{
			echo "<br><h2>Update existing chapter:</h2>";
		}
		echo "
		<fb:editor action='' method='POST'>
  
 		<fb:editor-text label='Chapter Name".$errors['group_name']."' name='group_name' value ='".$values['group_name']."'/>

 		<fb:editor-text label='Group Id".$errors['group_id']."' name='group_id' value ='".$values['group_id']."'/>
		
		 <fb:editor-text label='Address".$errors['street_address']."' name='street_address' value ='".$values['street_address']."'/>
		   <fb:editor-text label='Zip Code".$errors['zip']."' name='zip' value ='".$values['zip']."'/>

 		 <fb:editor-buttonset>";
 	 
 		 if ($action=="register") {
 		   echo "<fb:editor-button value='Register Chapter' name='register'/>";
 		 }	else {
 		   echo "<fb:editor-button value='Save Changes' name='edit'/>";
 		 }
 		 echo "</fb:editor-buttonset>
		</fb:editor>";
}

function validateGroupID($group_id,$action,$facebook) {
	if (!$facebook->api_client->groups_get("", $group_id))
		return 0;
	$check_query = "select * from groups where group_id = " . $group_id;
	$result = mysql_query($check_query);
	$rows = mysql_num_rows($result);
	if ($rows!=0 && $action=="register")
		return 0;
	return 1;
}

if (($register || $edit) && !($group_name && $group_id && $address && $zip)) {
	echo "All fields are required.";
	if ($register)
		displayForm($_POST,$errors,"register");
	else
		displayForm($_POST,$errors,"edit");
} elseif ($access_level==2) {
	if ($selected) {
		$result = mysql_query("SELECT * from groups WHERE group_id=".$selected);
		if ($result) {
			$row = mysql_fetch_array($result);
			foreach ($row as $key=>$value) {
				$values[$key]=$value;
			}
		}
		displayForm($values,$errors,"edit");
	} else if ($edit) {
		$valid_form = 1;
		$coords = address_checker($address, $zip);
		if (!validateGroupID($group_id,"edit",$facebook)) {
			echo $invalid_group_string;
			$valid_form = 0;
		} 
		if(!$coords['valid']){
			echo $invalid_address;
			$valid_form = 0;
		}

		if($valid_form) {
			$query = "UPDATE groups SET group_name='".$group_name . "', street_address='" . $address . "', zip=" . $zip .", latitude=".$coords['lat'].", longitude=".$coords['lng']. " WHERE group_id=".$group_id;
			mysql_query($query);	
			update_distances();
			header('Location: http://network.faceaids.org/facebook/group.php?group_id=' . $group_id);
		}else{
			displayForm($_POST,$errors,"edit");
		}
		
	} else if ($register) {
		$valid_form = 1;
		$coords = address_checker($address, $zip);
		
		if (!validateGroupID($group_id,"edit",$facebook)) {
			echo $invalid_group_string;
			$valid_form = 0;
		}
		
		if(!$coords['valid']){
			echo $invalid_address;
			$valid_form = 0;
		}

		if($valid_form) {
			$query = "INSERT INTO groups (group_name, group_id, street_address, zip, latitude, longitude) VALUES ('" . $group_name . "', " . $group_id .", '" . $address . "', '" . $zip ."', '".$coords['lat'] ."', '". $coords['lng'] . "')";
			mysql_query($query);
			update_distances();
			header('Location: http://network.faceaids.org/facebook/group.php?group_id=' . $group_id);
		}else{
			displayForm($_POST,$errors,"register");
		}
	} else {
		echo "<br><h2>Update existing chapters:</h2>
		<fb:editor action='' method='POST'>
		<fb:editor-custom label='Choose Group'>
			<select name='selected'>";
			
		$result3 = mysql_query("SELECT * from groups");
		while($row3 = mysql_fetch_array($result3)) {
			echo "<option value=".$row3['group_id'].">".$row3['group_name']."</option>";
		}
		echo	"</select>
			</fb:editor-custom>
		<fb:editor-buttonset>
		<fb:editor-button value='Edit'/>
		</fb:editor-buttonset>
		</fb:editor>";
		
		displayForm($values,$errors,"register");
	}
} elseif ($access_level==1) {
	//echo $admin_gid;
	if ($admin_gid && !$edit && !$register) {
		$result = mysql_query("SELECT * from groups WHERE group_id=".$admin_gid);
		if ($result) {
			$row = mysql_fetch_array($result);
			foreach ($row as $key=>$value) {
				$values[$key]=$value;
			}
		}
		displayForm($values,$errors,"edit");
	} else if (!$edit && !$register) {
		displayForm($values,$errors,"register");
	} else if ($edit) {
		$valid_form = 1;
		$coords = address_checker($address, $zip);

		if (!validateGroupID($group_id,"edit",$facebook)) {
			echo $invalid_group_string;
			$valid_form = 0;
		} 
		
		if(!$coords['valid']){
			echo $invalid_address;
			$valid_form = 0;
		}

		if($valid_form){
			$query = "UPDATE groups SET group_name='".$group_name . "', street_address='" . $address . "', zip='" . $zip ."' , latitude='".$coords['lat']."', longitude='".$coords['lng']. "' WHERE group_id=".$group_id;
			mysql_query($query);	
			update_distances();
			header('Location: http://network.faceaids.org/facebook/group.php?group_id=' . $group_id);
		}else{
			displayForm($_POST,$errors,"edit");
		}
	} else if ($register) {
		$valid_form = 1;
		$coords = address_checker($address, $zip);

		if (!validateGroupID($group_id,"edit",$facebook)) {
			echo $invalid_group_string;
			$valid_form = 0;
		} 
		
		if(!$coords['valid']){
			echo $invalid_address;
			$valid_form = 0;
		}
		
		if($valid_form){
			$query = "INSERT INTO groups (group_name, group_id, street_address, zip, latitude, longitude) VALUES ('" . $group_name . "', " . $group_id .", '" . $address . "', '" . $zip ."', '".$coords['lat'] ."', '". $coords['lng'] . "')";
			mysql_query($query);	
			update_distances();
			header('Location: http://network.faceaids.org/facebook/group.php?group_id=' . $group_id);
		}else{
			displayForm($_POST,$errors,"register");
		}
	}
}
?>