<?php

// API Call URL Constants
define('CRIME_AREA_COUNT_API_CALL_URL', '/warnerbros/crimes/count/area/');
define('CRIME_COMMITTED_DESCRIPTION_COUNT_API_CALL_URL', '/crimes/count/crime_committed/description/');
define('CRIME_COMMITTED_CODE_COUNT_API_CALL_URL', '/crimes/count/crime_committed/code/');
define('CRIME_COMMITTED_DESCRIPTION_ADDRESS_API_CALL_URL', '/crimes/address/all/crime_committed/description/');
define('CRIME_COMMITTED_CODE_ADDRESS_API_CALL_URL', '/crimes/address/all/crime_committed/code/');
define('CRIME_COMMITTED_DESCRIPTION_ADDRESS_PAGED_API_CALL_URL', '/crimes/address/page/crime_committed/description/');
define('CRIME_COMMITTED_CODE_ADDRESS_PAGED_API_CALL_URL', '/crimes/address/page/crime_committed/description/');

define('RESULTS_PER_SET', 1000);

include($_SERVER['DOCUMENT_ROOT'] . '/warnerbros/includes/master.inc');

// echo($_SERVER['REQUEST_URI']);
// print_object($_GET);

$api_call_data = retrieve_api_call($_SERVER['REQUEST_URI'], $_GET);
$db = retrieve_database_connection();
call_api($db, $api_call_data);

// PHP Functions

// Determine what call to make based on the URL
function retrieve_api_call($url, $url_parameters) {
	
	switch($url) {
		case strstr($url, CRIME_AREA_COUNT_API_CALL_URL):
			$array['url'] = CRIME_AREA_COUNT_API_CALL_URL;
			break;
		case ($url_parameters['type'] == 'count' && strstr($url, CRIME_COMMITTED_DESCRIPTION_COUNT_API_CALL_URL)):
			$array['url'] = CRIME_COMMITTED_DESCRIPTION_COUNT_API_CALL_URL;
			break;
		case ($url_parameters['type'] == 'count' && strstr($url, CRIME_COMMITTED_CODE_COUNT_API_CALL_URL)):
			$array['url'] = CRIME_COMMITTED_CODE_COUNT_API_CALL_URL;
			break;
		case ($url_parameters['type'] == 'address' && $url_parameters['records'] == 'all' && strstr($url, CRIME_COMMITTED_DESCRIPTION_ADDRESS_API_CALL_URL) && isset($url_parameters['crime_committed_description'])):
			$array['url'] = CRIME_COMMITTED_DESCRIPTION_ADDRESS_API_CALL_URL;
			break;
		case ($url_parameters['type'] == 'address' && $url_parameters['records'] == 'all' && strstr($url, CRIME_COMMITTED_CODE_ADDRESS_API_CALL_URL) && isset($url_parameters['crime_committed_code'])):
			$array['url'] = CRIME_COMMITTED_CODE_ADDRESS_API_CALL_URL;
			break;			
		case ($url_parameters['type'] == 'address' && is_numeric($url_parameters['page']) && isset($url_parameters['crime_committed_description'])):
			$array['url'] = CRIME_COMMITTED_DESCRIPTION_ADDRESS_PAGED_API_CALL_URL;
			break;
		case ($url_parameters['type'] == 'address' && is_numeric($url_parameters['page']) && isset($url_parameters['crime_committed_code'])):
			$array['url'] = CRIME_COMMITTED_CODE_ADDRESS_PAGED_API_CALL_URL;
			break;			
		default:
			// Invalid URL
	}
	
	$array['parameters'] = $url_parameters;
	return $array;	
	
}

// Use prepared statements with parameterized queriues to prevent SQL injection
function call_api($db, $api_call_data) {

	// print_object($api_call_data);
	
	// These are for API call URLs that only have a query parameter at the end of the URL
	switch($api_call_data['url']) {
		case CRIME_AREA_COUNT_API_CALL_URL:
			$query = "SELECT COUNT(1) AS total_crimes FROM reported_crimes WHERE area_name = ?;";		
			$statement = $db->prepare($query);
			$statement->bind_param("s", clean_url_parameter($api_call_data['parameters']['area_name']));
			$statement->execute();
			$result = $statement->get_result();
			$response = ($result) ? $result->fetch_assoc() : '';
			print_object(json_encode($response));
			$statement->close();		
			break;
		case CRIME_COMMITTED_DESCRIPTION_COUNT_API_CALL_URL:
			$query = "SELECT COUNT(1) AS total_crimes FROM reported_crimes WHERE crime_committed_description = ?;";		
			$statement = $db->prepare($query);
			$statement->bind_param("s", clean_url_parameter($api_call_data['parameters']['crime_committed_description']));
			$statement->execute();
			$result = $statement->get_result();
			$response = ($result) ? $result->fetch_assoc() : '';
			print_object(json_encode($response));
			$statement->close();		
			break;
		case CRIME_COMMITTED_CODE_COUNT_API_CALL_URL:
			$query = "SELECT COUNT(1) AS total_crimes FROM reported_crimes WHERE crime_committed_code = ?;";		
			$statement = $db->prepare($query);
			$statement->bind_param("i", clean_url_parameter($api_call_data['parameters']['crime_committed_code']));
			$statement->execute();
			$result = $statement->get_result();
			$response = ($result) ? $result->fetch_assoc() : '';
			print_object(json_encode($response));
			$statement->close();		
			break;
		case CRIME_COMMITTED_DESCRIPTION_ADDRESS_API_CALL_URL:
			$query = "SELECT location, cross_street, date_reported FROM reported_crimes WHERE crime_committed_description = ? ORDER BY date_reported DESC;";		
			$statement = $db->prepare($query);
			$statement->bind_param("s", clean_url_parameter($api_call_data['parameters']['crime_committed_description']));
			$statement->execute();
			$result = $statement->get_result();
			
			$response = array();
			
			while($row = $result->fetch_assoc()) {
				$row['location'] = clean_white_space($row['location']);
				$row['cross_street'] = clean_white_space($row['cross_street']);
				array_push($response, $row);
			}
			
			print_object(json_encode($response));
			$statement->close();		
			break;
		case CRIME_COMMITTED_CODE_ADDRESS_API_CALL_URL:
			$query = "SELECT location, cross_street, date_reported FROM reported_crimes WHERE crime_committed_code = ? ORDER BY date_reported DESC;";			
			$statement = $db->prepare($query);
			$statement->bind_param("i", clean_url_parameter($api_call_data['parameters']['crime_committed_code']));
			$statement->execute();
			$result = $statement->get_result();
			
			$response = array();
			
			while($row = $result->fetch_assoc()) {
				$row['location'] = clean_white_space($row['location']);
				$row['cross_street'] = clean_white_space($row['cross_street']);
				array_push($response, $row);
			}
			
			print_object(json_encode($response));
			$statement->close();		
			break;			
	}
	
	// For the case of paged API URL calls, set them up here
	if($api_call_data['parameters']['type'] == 'address' && is_numeric($api_call_data['parameters']['page']) && isset($api_call_data['parameters']['crime_committed_description'])) {
		$offset = ($api_call_data['parameters']['page'] - 1) * RESULTS_PER_SET;
		$limit = $offset . ', ' . RESULTS_PER_SET;
		$query = "SELECT location, cross_street, date_reported FROM reported_crimes WHERE crime_committed_description = ? ORDER BY date_reported DESC LIMIT $limit";
		$statement = $db->prepare($query);
		$statement->bind_param("s", clean_url_parameter($api_call_data['parameters']['crime_committed_description']));
		$statement->execute();
		$result = $statement->get_result();
		
		$response = array();
		
		while($row = $result->fetch_assoc()) {
			$row['location'] = clean_white_space($row['location']);
			$row['cross_street'] = clean_white_space($row['cross_street']);
			array_push($response, $row);
		}
		
		print_object(json_encode($response));
		$statement->close();		
	}
	
	if($api_call_data['parameters']['type'] == 'address' && is_numeric($api_call_data['parameters']['page']) && isset($api_call_data['parameters']['crime_committed_code'])) {
		$offset = ($api_call_data['parameters']['page'] - 1) * RESULTS_PER_SET;
		$limit = $offset . ', ' . RESULTS_PER_SET;
		$query = "SELECT location, cross_street, date_reported FROM reported_crimes WHERE crime_committed_code = ? ORDER BY date_reported DESC LIMIT $limit";
		$statement = $db->prepare($query);
		$statement->bind_param("i", $api_call_data['parameters']['crime_committed_code']);
		$statement->execute();
		$result = $statement->get_result();
		
		$response = array();
		
		while($row = $result->fetch_assoc()) {
			$row['location'] = clean_white_space($row['location']);
			$row['cross_street'] = clean_white_space($row['cross_street']);
			array_push($response, $row);
		}
		
		print_object(json_encode($response));
		$statement->close();		
	}	
}

function clean_url_parameter($url_parameter) {
	return str_replace('_', ' ', $url_parameter);
}

function clean_white_space($string) {
	return preg_replace('/\s+/', ' ', $string);
}

?>