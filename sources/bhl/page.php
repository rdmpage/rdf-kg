<?php

require_once('../../resolvers/lib.php');


// page

$PageID = 15775524;

	
	$parameters = array(
		'op' => 'GetPageMetadata',
		'pageid' => $PageID,
		'ocr' => 'false',
		'names' => 'true',
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);
	
	
	
	
	$url = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);
		
		
		
$ItemID = 53706;
		
		$parameters = array(
		'op' => 'GetItemMetadata',
		'itemid' => $ItemID,
		'pages' => 'false',
		'ocr' => 'false',
		'parts' => 'false',
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);
	
	
	$url = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);
	/*
	// part (this query gets BioStor id (and others)
		$parameters = array(
		'op' => 'GetPartMetadata',
		'partid' => 52921,
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);
	$url = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);
	*/
		
	$json = get($url);
	
	echo $json;
	
	
	$obj = json_decode($json);
	
	print_r($obj);
	
	
?>


