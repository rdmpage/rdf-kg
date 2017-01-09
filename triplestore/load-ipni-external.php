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


foreach ($lsids as $lsid)
{
	$url = 'http://ipni.org/' . $lsid;
	$rdf = get($url, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/600.5.17 (KHTML, like Gecko) Version/8.0.5 Safari/600.5.17');

	echo $rdf;

	// convert to triples
	$triples = rdf_to_triples($rdf);

	echo $triples;

	upload_data($triples);

}

?>

