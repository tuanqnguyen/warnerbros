<?php

// PHP Functions

function print_object($object) {
	
	echo('<pre>'); print_r($object); echo('</pre>');
	
}

// For running insert/update/delete queries
function run_query($db, $query) {
	
	if(!$db->query($query)) {
		echo('Error description: ' . $db->error . '<hr />');
	}
}

function run_select_query($db) {
	
	
	
}

?>