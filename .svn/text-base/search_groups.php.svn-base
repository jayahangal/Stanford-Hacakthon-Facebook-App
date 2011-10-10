<?php
include_once 'facebook_include.php';

$group_name = $_POST['group_name'];
$zip = $_POST['zip'];
if($group_name == "" && $zip == "")
{
	echo "
	<fb:editor action='search_groups.php'>
	
	  <fb:editor-text label='Chapter Name' name='group_name' value =''/>
	  <fb:editor-custom>OR</fb:editor-custom> 
	  <fb:editor-text label='Zip Code' name='zip' value =''/>	
	
	  <fb:editor-buttonset>
	    <fb:editor-button value='Search'/>
	  </fb:editor-buttonset>
	</fb:editor>";
}
else
{
	header('Location: http://network.faceaids.org/facebook/search.php?group_name=' . $group_name . "&zip=". $zip);
}

?>