<?php

include($_SERVER['DOCUMENT_ROOT'] . '/warnerbros/includes/master.inc');

$array['response'] = 'Not valid URL';
print_object(json_encode($array));

?>
