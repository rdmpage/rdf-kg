<?php

require(dirname(dirname(dirname(__FILE__))) . '/documentstore/couchsimple.php');

// Parse Mendeley list of documents

$filename = 'gbif-public-100.json';

$json = file_get_contents($filename);

$obj = json_decode($json);

foreach ($obj as $doc)
{
	
	
	// format
	
	$doc->format = 'application/vnd.mendeley-document.1+json';
	
	// Generate global id
	$doc->_id = $doc->id;
	
	
	// Store
	
	
	print_r($doc);
	
	$couch->add_update_or_delete_document($doc, $doc->_id);
	
}

?>