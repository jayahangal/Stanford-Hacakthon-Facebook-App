<?php
	define("MAPS_HOST", "maps.google.com");
	define("KEY", "ABQIAAAAxGlW3vR-ctmoXiK_Qx655hRI6c91rUMYWqolaCInUnbo8XfAyBSnngw-xshy_eCpJBTbF4GHzKzAGQ");

	// Initialize delay in geocode speed
	$delay = 0;
	$base_url = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . KEY;
?>