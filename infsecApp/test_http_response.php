<?php

	$postdata = http_build_query(
    array(
        'IMEI' => '123456789012345',
        'LAT' => '11.11111',
		'LONG' => '11.11111',
		'ACC' => '3.3'
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context = stream_context_create($opts);

$result = file_get_contents('http://localhost/infsecApp/store.php', false, $context);

echo $result;
?>
