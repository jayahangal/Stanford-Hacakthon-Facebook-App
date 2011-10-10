<?php
 
function address_checker($street_address, $zip_code){
	include "google_maps.php";
	$geocode_pending = true;
	$street_address = $_POST['street_address'];
	$zip_code = $_POST['zip_code'];

	while ($geocode_pending) {
		$address = $street_address . ", " . $zip_code . ", USA";
		$request_url = $base_url . "&q=" . urlencode($address);
		$xml = simplexml_load_file($request_url) or die("url not loading");
		$num_placemarks = count($xml->Response->Placemark);
		$accuracy = $xml->Response->Placemark->AddressDetails['Accuracy'];
		$status = $xml->Response->Status->code;
		if (strcmp($status, "200") == 0 && $num_placemarks <= 2 && $accuracy >=5) {
			// Successful geocode
			$geocode_pending = false;
			$coordinates = $xml->Response->Placemark->Point->coordinates;
			$coordinatesSplit = split(",", $coordinates);
			// Format: Longitude, Latitude, Altitude
			$coords['lat'] = $coordinatesSplit[1];
			$coords['lng'] = $coordinatesSplit[0];
			$coords['valid'] = 1;

			return $coords;

		} else if (strcmp($status, "620") == 0) {
		  // sent geocodes too fast
			$delay += 100000;
		} else {
		  // failure to geocode
			$geocode_pending = false;
			$coords['lat'] = 0;
			$coords['lng'] = 0;
			$coords['valid'] = 0;
			return $coords;
		}
		usleep($delay);
	}
}
?>