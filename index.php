<?php

// API Call Samples

$array = array(
	'/warnerbros/crimes/count/area/SouthWest',
	'/warnerbros/crimes/count/area/Central',
	'/warnerbros/crimes/count/area/77th_Street',
	
	'/warnerbros/crimes/count/crime_committed/description/battery_-_simple_assault',
	'/warnerbros/crimes/count/crime_committed/description/arson',
	'/warnerbros/crimes/count/crime_committed/description/burglary',
	
	'/warnerbros/crimes/count/crime_committed/code/624',
	'/warnerbros/crimes/count/crime_committed/code/745',
	'/warnerbros/crimes/count/crime_committed/code/740',
	
	'/warnerbros/crimes/address/all/crime_committed/description/BATTERY_-_SIMPLE_ASSAULT',
	'/warnerbros/crimes/address/page/1/crime_committed/description/BATTERY_-_SIMPLE_ASSAULT',
	'/warnerbros/crimes/address/page/3/crime_committed/description/BATTERY_-_SIMPLE_ASSAULT',
	'/warnerbros/crimes/address/page/6/crime_committed/description/BATTERY_-_SIMPLE_ASSAULT',
	
	'/warnerbros/crimes/address/all/crime_committed/code/624',
	'/warnerbros/crimes/address/page/1/crime_committed/code/624',
	'/warnerbros/crimes/address/page/2/crime_committed/code/624',
	'/warnerbros/crimes/address/page/3/crime_committed/code/624'
);


?>

<p>Click on the following links below to test the API out and receive JSON output:</p>

<ul>
	<?php foreach($array as $key => $value): ?>
		<li><a target="_blank" href="<?=$value;?>"><?=$value;?></a></li>
	<?php endforeach; ?>
</ul>