<?php

// Jena-Fuseki API

require_once(dirname(dirname(__FILE__)) . '/sources/lib.php');

$config['fuseki-url'] 		= 'http://rdmpage-jena-fuseki-v.sloppy.zone/';
$config['fuseki-dataset'] 	= 'dataone';
$config['fuseki-user'] 		= 'admin';
$config['fuseki-password'] 	= '0LEople75CaPVx4';

// If password lost in logs get from comamnd line
// sloppy logs -n 10000 new-project | grep "admin="

//----------------------------------------------------------------------------------------
// $triples_filename is the full path to a file of triples
function upload_from_file($triples_filename)
{
	global $config;
	
	$url = $config['fuseki-url'] . $config['fuseki-dataset'];

	$filename = basename($triples_filename);

	$data = array(
		'uploaded_file' => curl_file_create(
			$triples_filename, 
			'application/n-triples', 
			$filename
		)
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	curl_close($ch);

	echo $response;
}

//----------------------------------------------------------------------------------------
// $data is a string of triples
function upload_data($data)
{
	global $config;
	
	$url = $config['fuseki-url'] . $config['fuseki-dataset'];
	
	echo $url . "\n";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/n-triples"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	if ($http_code != 200)
	{
		echo $response;	
		die ("Triple store returned $http_code\n");
	}
	
	
	curl_close($ch);

	echo $response;
}

//----------------------------------------------------------------------------------------
// Load a triples file, do it in chuncks as it could be large
// $triples_filename is the full path to a file of triples
function upload_from_file_chunks($triples_filename, $chunks = 1000)
{
	global $config;
	
	$url = $config['fuseki-url'] . $config['fuseki-dataset'];

	$count = 0;
	$triples = '';

	
	$file_handle = fopen($triples_filename, "r");
	while (!feof($file_handle)) 
	{
		$line = fgets($file_handle);
		$triples .= $line;
		
		if (!(++$count < $chunks))
		{
			//echo $triples;
			
			upload_data($triples);
			
			echo $count . "\n";
			$count = 0;
			$triples = '';
		}
	}
			

}

//----------------------------------------------------------------------------------------
// query
function sparql_query($query)
{
	global $config;
	
	$url = $config['fuseki-url'] . $config['fuseki-dataset'];
	
	// Query is string
	$data = 'query=' . urlencode($query);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); 
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/sparql-results+json"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	if ($http_code != 200)
	{
		echo $response;	
		die ("Triple store returned $http_code\n");
	}
	
	
	curl_close($ch);

	echo $response;




}


// test
if (0)
{
	$triples_filename = dirname(__FILE__) . '/data/mendeley_group.nt';
	upload_from_file_chunks($triples_filename);



}

if (1)
{
	$sparql = 'SELECT *
WHERE {
  ?occurrence ?y "BC ZSM Lep 10234" .
   ?occurrence <http://schema.org/name> ?name .
  ?occurrence <http://schema.org/alternateName> ?z .
   
} ';

	sparql_query($sparql);
}



?>