<?php

require_once(dirname(dirname(__FILE__)) . '/sources/lib.php');
require_once(dirname(dirname(__FILE__)) . '/documentstore/couchsimple.php');
require_once(dirname(__FILE__) . '/triple_store.php');

// Load triples from CouchDB into ARC triple store

global $store_config;

$rows_per_page = 100;
$skip = 0;


$done = false;
while (!$done)
{
	$url = 'http://127.0.0.1:5984/rdf_kg/_design/mendeley_group/_list/n-triples/nt';

	// ORCID triples
	$url = 'http://127.0.0.1:5984/rdf_kg/_design/orcid/_list/n-triples/nt';

	// IPNI triples
	$url = 'http://localhost/~rpage/ipni-names/ipni.nt';

	// ORCID example
	$url = 'http://127.0.0.1:5984/rdf_kg/_design/orcid/_list/n-triples/nt';
	$url .= '?key=' . urlencode("\"http://orcid.org/0000-0001-7436-0939\"");
	
	// CrossRef triples
	$url = 'http://127.0.0.1:5984/rdf_kg/_design/crossref/_list/n-triples/nt';

	$url .= '?limit=' . $rows_per_page . '&skip=' . $skip;
	
	$data = get($url);
	
	//echo $url
	//echo $data;
	
	$rows = explode("\n", $data);
	$n = count($rows);
	
	$skip += $rows_per_page;
	$done = ($n < $rows_per_page);
	
	//$done = true;
	
		
	//echo $data;
	
	$query = 'LOAD <' . $url . '>';
		
	$r = $store->query($query);
	
	print_r($r['result']);
	echo 'Query took ' . $r['query_time'] . ' seconds.' . "\n";	
	
	//exit();
	
	// store...
}

?>
