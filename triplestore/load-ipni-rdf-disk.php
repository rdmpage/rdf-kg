<?php

require_once(dirname(dirname(__FILE__)) . '/sources/lib.php');
require_once(dirname(__FILE__) . '/arc2/ARC2.php');
require_once(dirname(__FILE__) . '/fuseki.php');


//--------------------------------------------------------------------------------------------------
function rdf_to_triples($xml)
{	
	// Parse RDF into triples
	$parser = ARC2::getRDFParser();		
	$base = 'http://example.com/';
	$parser->parse($base, $xml);	
	
	$triples = $parser->getTriples();
	
	//print_r($triples);
	
	// clean up
	
	$cleaned_triples = array();
	foreach ($triples as $triple)
	{
		$add = true;

		if ($triple['s'] == 'http://example.com/')
		{
			$add = false;
		}
		
		if ($add)
		{
			$cleaned_triples[] = $triple;
		}
	}
	
	return $parser->toNTriples($cleaned_triples);
	

}

$lsids = array(
'urn:lsid:ipni.org:names:77104344-1'
);


$basedir = '/Volumes/WD Elements 1TB/rdf-archive/ipni/rdf';

foreach ($lsids as $lsid)
{
	$id = $lsid;
	$id = str_replace('urn:lsid:ipni.org:names:', '', $id);
	
	$rdf_id = preg_replace('/-\d+$/', '', $id);
		
	$dir = floor($rdf_id / 1000);
		
	$filename = $basedir .'/' . $dir . '/' . $id . '.xml';

	$rdf = file_get_contents($filename);
	echo $rdf;

	// convert to triples
	$triples = rdf_to_triples($rdf);

	echo $triples;

	upload_data($triples);

}

?>

