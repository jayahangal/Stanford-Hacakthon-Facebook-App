<?php 
include_once 'facebook_include.php';
?>

<fb:request-form
	action = "requests_points.php"
	method = "POST"
	invite = "true" 
	type = "FACE AIDS" 
	content = "Connect with the FACE AIDS community.<?php echo htmlentities("<fb:req-choice url = 'http://apps.facebook.com/face_aids' label = 'Join'") ?>"
>				
	<fb:multi-friend-selector  showborder='false' actiontext='Invite your friends to use the FACE AIDS Application.'>
</fb:request-form>					
