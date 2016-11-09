<?php

// Test DwCA reader

require_once('dwca.php');

//--------------------------------------------------------------------------------------------------

// Archive to parse
$basedir = '0028114-160910150852091/';


// meta.xml tells us how to interpret archive
$filename = $basedir . 'meta.xml';
$xml = file_get_contents($filename);

// Read details of source file(s) and extract data
$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('dwc_text', 'http://rs.tdwg.org/dwc/text/');

//parse_eml($xpath);

// set a custom function to determine how we post-process the data
parse_meta($xpath, '//dwc_text:core', 'data_display');
parse_meta($xpath, '//dwc_text:extension', 'data_display');

//print_r($geometry);

//echo json_encode($geometry);

?>
