<?php


// Add/update data to Fuseki from CouchDB

require_once(dirname(dirname(__FILE__)) . '/sources/lib.php');
require_once(dirname(dirname(__FILE__)) . '/documentstore/couchsimple.php');

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
// Add all triples assocoated with one record
function add_one($id, $view)
{
	global $config;
	global $couch;
		
	$url = '_design/' . $view . '/_view/nt?key=' . urlencode('"' . $id . '"');
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	if ($resp)
	{
		$response_obj = json_decode($resp);
		if (!isset($response_obj->error))
		{
			$nt = '';
			foreach ($response_obj->rows as $row)
			{
				$nt .=  $row->value . "\n";
			}	
			
			// POST
			upload_data($nt);
			
				
		}
	}		
}

//----------------------------------------------------------------------------------------
// Get all objects modified after a certain date and add corresponding triples
// Need to do this for a given view
function add_modified($view, $from = null)
{
	global $config;
	global $couch;
		
	$url = '_design/' . $view . '/_view/modified';
	
	//$start_key =
	if ($from)
	{
		$start_key = $from;
	}
	else
	{
		// Make October 1 2016 the "start date"
		$start_key = '2016-10-01'; //date("c", time());
	}
	$end_key = date("c", time());
	
	$url .= '?start_key=' . urlencode('"' . $start_key . '"') . '&end_key='. urlencode('"' . $end_key . '"');
	
	echo $url . "\n";
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	if ($resp)
	{
		$response_obj = json_decode($resp);
		if (!isset($response_obj->error))
		{
			foreach ($response_obj->rows as $row)
			{
				echo $row->id . "\n";
				
				add_one($row->id, $view);
			}	
		}
	}		

}

//----------------------------------------------------------------------------------------
// Get all triples in a view
function add_view($view, $rows_per_page=1000, $skip=0)
{
	global $config;
	global $couch;
	
	$done = false;
	while (!$done)
	{
		$url = '_design/' . $view . '/_view/nt';
		
		$url .= '?limit=' . $rows_per_page . '&skip=' . $skip;
		
		echo $url . "\n";
	
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

		if ($resp)
		{
			$response_obj = json_decode($resp);
			if (!isset($response_obj->error))
			{
				$n = count($response_obj->rows);
				
				$nt = '';
				foreach ($response_obj->rows as $row)
				{
					$nt .=  $row->value . "\n";
				}	
			
				// POST
				upload_data($nt);
			}
		}
		
		$skip += $rows_per_page;
		$done = ($n < $rows_per_page);			
	}
}

//----------------------------------------------------------------------------------------
// Get all triples in a view
function debug_view($view, $rows_per_page, $skip)
{
	global $config;
	global $couch;
	
	$url = '_design/' . $view . '/_view/nt';
	
	$url .= '?limit=' . $rows_per_page . '&skip=' . $skip;
	
	echo $url . "\n";

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	if ($resp)
	{
		$response_obj = json_decode($resp);
		if (!isset($response_obj->error))
		{
			$n = count($response_obj->rows);
			
			$nt = '';
			foreach ($response_obj->rows as $row)
			{
				$nt .=  $row->value . "\n";
			}	
		
			echo $nt;
		}
	}
}



//add_one("http://dx.doi.org/10.1655/08-040r.1", "crossref");

//add_one("http://dx.doi.org/10.7601/mez.56.275", "crossref");


$day = 60 * 60 * 24;

$start_time = date("c", time() - $day);

$start_time = date("c", time() - (60 * 60)); // hour

//$start_time = date("c", time() - (60 * 10)); // last ten minutes

//add_modified('crossref', $start_time);
// add_modified('gbif-occurrence', $start_time);
//add_modified('bold', $start_time);
//add_modified('genbank', $start_time);

//add_modified('bhl', $start_time);

//add_modified('gbif-species', $start_time);

// IPNI example
//add_modified('orcid', $start_time);
//add_modified('crossref', $start_time);

add_modified('gbif-occurrence', $start_time);
add_modified('gbif-species', $start_time);
add_modified('genbank', $start_time);
add_modified('crossref', $start_time);


//add_view('mendeley_group');

/*
if (1)
{
	//add_view('orcid');
	add_view('orcid', 1000, 1646000);
}
else
{
	debug_view('orcid', 1000, 1424000);
}
*/




//add_modified('orcid');
//add_modified('crossref', $start_time);
//add_modified('worldcat');


//upload_from_file(dirname(__FILE__) . '/data/mendeley_group.nt');



?>