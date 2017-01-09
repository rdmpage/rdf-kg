<?php


// Add/update data to Fuseki


$dataset = 'data';
$dataset = 'dataone';

//$url = 'http://rdmpage-jena-fuseki.sloppy.zone/' . $dataset;
$url = 'http://rdmpage-jena-fuseki-v.sloppy.zone/' . $dataset;

$username = 'admin';
$password = '5eD9digriOtft2J';


$triples_filename = dirname(__FILE__) . '/mendeley_group.nt';
$triples_filename = dirname(__FILE__) . '/crossref.nt';

$triples_filename = '/Users/rpage/Sites/ipni-names/ipni.nt';




$filename = basename($triples_filename);

$data = array(
    'uploaded_file' => curl_file_create(
    	$triples_filename, 
    	'application/n-triples', 
    	$filename
    )
);

print_r($data);

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

?>