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

$urls = array(
'http://purl.uniprot.org/taxonomy/1002741',
'http://purl.uniprot.org/taxonomy/1118496',
'http://purl.uniprot.org/taxonomy/1118521',
'http://purl.uniprot.org/taxonomy/1118522',
'http://purl.uniprot.org/taxonomy/1118527',
'http://purl.uniprot.org/taxonomy/1118541',
'http://purl.uniprot.org/taxonomy/1118845',
'http://purl.uniprot.org/taxonomy/1160812',
'http://purl.uniprot.org/taxonomy/1160816',
'http://purl.uniprot.org/taxonomy/1160817',
'http://purl.uniprot.org/taxonomy/1160835',
'http://purl.uniprot.org/taxonomy/1160836',
'http://purl.uniprot.org/taxonomy/118181',
'http://purl.uniprot.org/taxonomy/1333337',
'http://purl.uniprot.org/taxonomy/1496111',
'http://purl.uniprot.org/taxonomy/1546130',
'http://purl.uniprot.org/taxonomy/1546131',
'http://purl.uniprot.org/taxonomy/1546132',
'http://purl.uniprot.org/taxonomy/1546133',
'http://purl.uniprot.org/taxonomy/46474',
'http://purl.uniprot.org/taxonomy/46489',
'http://purl.uniprot.org/taxonomy/46490',
'http://purl.uniprot.org/taxonomy/8893',
'http://purl.uniprot.org/taxonomy/8894',
'http://purl.uniprot.org/taxonomy/8895',
'http://purl.uniprot.org/taxonomy/8897'
);

$urls=array(
'http://purl.uniprot.org/taxonomy/190670',
'http://purl.uniprot.org/taxonomy/190673',
'http://purl.uniprot.org/taxonomy/190674',
'http://purl.uniprot.org/taxonomy/190689',
'http://purl.uniprot.org/taxonomy/190690',
'http://purl.uniprot.org/taxonomy/190699',
'http://purl.uniprot.org/taxonomy/190700',
'http://purl.uniprot.org/taxonomy/207698',
'http://purl.uniprot.org/taxonomy/207699',
'http://purl.uniprot.org/taxonomy/207701',
'http://purl.uniprot.org/taxonomy/207702',
'http://purl.uniprot.org/taxonomy/207703',
'http://purl.uniprot.org/taxonomy/207704',
'http://purl.uniprot.org/taxonomy/207705',
'http://purl.uniprot.org/taxonomy/243056',
'http://purl.uniprot.org/taxonomy/243317',
'http://purl.uniprot.org/taxonomy/243318',
'http://purl.uniprot.org/taxonomy/243319',
'http://purl.uniprot.org/taxonomy/1160838',
'http://purl.uniprot.org/taxonomy/1160839',
'http://purl.uniprot.org/taxonomy/1160840',
'http://purl.uniprot.org/taxonomy/1160841',
'http://purl.uniprot.org/taxonomy/1160842',
'http://purl.uniprot.org/taxonomy/1160848'
);

$urls=array(
'http://purl.uniprot.org/taxonomy/243321',
'http://purl.uniprot.org/taxonomy/243322',
'http://purl.uniprot.org/taxonomy/243323',
'http://purl.uniprot.org/taxonomy/243324',
'http://purl.uniprot.org/taxonomy/243325',
'http://purl.uniprot.org/taxonomy/326917',
'http://purl.uniprot.org/taxonomy/381020',
'http://purl.uniprot.org/taxonomy/46475',
'http://purl.uniprot.org/taxonomy/46476',
'http://purl.uniprot.org/taxonomy/46477',
'http://purl.uniprot.org/taxonomy/46478',
'http://purl.uniprot.org/taxonomy/46479',
'http://purl.uniprot.org/taxonomy/46480',
'http://purl.uniprot.org/taxonomy/46481',
'http://purl.uniprot.org/taxonomy/46482',
'http://purl.uniprot.org/taxonomy/46483',
'http://purl.uniprot.org/taxonomy/46484',
'http://purl.uniprot.org/taxonomy/46485',
'http://purl.uniprot.org/taxonomy/46486',
'http://purl.uniprot.org/taxonomy/46487',
'http://purl.uniprot.org/taxonomy/46488',
'http://purl.uniprot.org/taxonomy/46491',
'http://purl.uniprot.org/taxonomy/46492',
'http://purl.uniprot.org/taxonomy/46493',
'http://purl.uniprot.org/taxonomy/46495'
);

$urls=array(
'http://purl.uniprot.org/taxonomy/46496',
'http://purl.uniprot.org/taxonomy/46497',
'http://purl.uniprot.org/taxonomy/46498',
'http://purl.uniprot.org/taxonomy/46499',
'http://purl.uniprot.org/taxonomy/46500',
'http://purl.uniprot.org/taxonomy/46501',
'http://purl.uniprot.org/taxonomy/46502',
'http://purl.uniprot.org/taxonomy/46504',
'http://purl.uniprot.org/taxonomy/46505',
'http://purl.uniprot.org/taxonomy/46511',
'http://purl.uniprot.org/taxonomy/465450',
'http://purl.uniprot.org/taxonomy/518920',
'http://purl.uniprot.org/taxonomy/555232',
'http://purl.uniprot.org/taxonomy/56283',
'http://purl.uniprot.org/taxonomy/670358',
'http://purl.uniprot.org/taxonomy/670359',
'http://purl.uniprot.org/taxonomy/8896'
);

foreach ($urls as $url)
{
	$rdf = get($url, '', 'application/rdf+xml');

	echo $rdf;

	// convert to triples
	$triples = rdf_to_triples($rdf);

	echo $triples;

	$graph = 'http://sparql.uniprot.org/taxonomy';
	//$graph = '';

	upload_data($triples, $graph);
	//upload_data($triples);

}

?>

