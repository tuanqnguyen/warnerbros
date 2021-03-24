<?php

// Import Crime_Data_from_2020_to_Present.csv into the database

/*
set_time_limit(0);

include($_SERVER['DOCUMENT_ROOT'] . '/warnerbros/includes/master.inc');

$csv_file = $_SERVER['DOCUMENT_ROOT'] . '/warnerbros/database/Crime_Data_from_2020_to_Present.csv';

$db = retrieve_database_connection();
process_csv_data($db, $csv_file);
*/

// PHP Functions

function process_csv_data($db, $csv_file) {
	
	// $counter = 1;
	// $max_records = 200;
	
	if(($handle = fopen($csv_file, 'r')) !== FALSE) {
		
		// while(($data = fgetcsv($handle, 1000, ",")) !== FALSE && $counter <= $max_records) {
		while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {	
			
			$row++;
			if($row == 1) { continue; } // Skip the header row, do not import it into the database
			
			$num = count($data);
			echo "<p>Record #$row: $num fields: <br /></p>\n";
			
			$data_values = '';
			
			for($c = 0; $c < $num; $c++) {
				
				$value;
				
				switch($c) {
					case 1:
					case 2: // Fields 1 and 2 are date reported and date occurred, need date formatting
						$value = convert_date($data[$c]); 
						break;
					case 3: // Field 3 is time occurred, need time formatting
						$value = convert_time($data[$c]); 
						break;	
					case 16: // Field 16 is used weapon code, needs integer check 
					case 21:
					case 22:
					case 23:
					case 24: // Fields 21 - 24 are crime comitted codes, need integer check
						$value = check_number_data($data[$c]); 
						break;
					default:
						$value = $data[$c];
				}
				
				$data_values .= "'" . addslashes($value) . "',";
				
				// echo $value . "<br />\n";

			}
			
			// Remove last comma from $data_values
			$data_values = rtrim($data_values, ',');
			insert_data($db, $data_values);
			
			// echo('<hr />');
			
			$counter++;
			
		}
		
		fclose($handle);
		
	}
	
	echo('Done.');

}

function convert_date($date) {
	// 01/08/2020 12:00:00 AM -> 2020-01-08
	$array = explode(' ', $date);
	$date = $array[0];
	$array = explode('/', $date);
	return $array[2] . '-' . $array[0] . '-' . $array[1];
}

function convert_time($time) {
	// 2230 -> 22:30:00 (10:30 PM)
	$hour = substr($time, 0, 2);
	$time = substr($time, 2, 4);
	return $hour . ':' . $time . ':00';
}

function insert_data($db, $data) {
	
	$query = "INSERT INTO 
				reported_crimes
				(
					report_number,
					date_reported,
					date_occurred,
					time_occurred,
					area,
					area_name,
					report_district_number,
					part_1_2,
					crime_committed_code,
					crime_committed_description,
					mo_codes,
					victim_age,
					victim_gender,
					victim_descent,
					premis_code,
					premis_description,
					weapon_used_code,
					weapon_description,
					status,
					status_description,
					crime_comitted_code_1,
					crime_comitted_code_2,
					crime_comitted_code_3,
					crime_comitted_code_4,
					location,
					cross_street,
					latitude,
					longitude				
				)
				VALUES
					($data);";
	
	// echo($query . '<br />');
	run_query($db, $query);
}

function check_number_data($value) {
	return ($value == '') ? 0 : $value;
}

?>