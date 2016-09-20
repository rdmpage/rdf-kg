<?php

// Harvest records from a Mendeley group

require(dirname(dirname(dirname(__FILE__))) . '/documentstore/couchsimple.php');


// go to https://mendeley-show-me-access-tokens.herokuapp.com to get

$token = 'MSwxNDc0Mzc0MTgzOTExLDU3MjQsMTAyOCxhbGwsLCwsNDA3MGQxMjItYjNhZC0zZWRlLTk3ZWEtZDIzMzc3MGMzNjZhLDZrMmRwUFYxdHFyRVR0Nk9Hemp4OXFKNW1ORQ'; 
// UUID of group, hard to get, need to use Mendeley API https://api.mendeley.com/groups
// to list groups user belongs to
$group_id = 'dcb8ff61-dbc0-3519-af76-2072f22bc22f'; // GBIF
//$group_id = 'b60221dd-f820-3c0f-b0a8-4dfba36cf763'; // iPad


//----------------------------------------------------------------------------------------
//http://php.net/manual/pl/function.http-parse-headers.php#112986
function http_parse_headers($raw_headers)
{
	$headers = array();
	$key = ''; // [+]

	foreach(explode("\n", $raw_headers) as $i => $h)
	{
		$h = explode(':', $h, 2);

		if (isset($h[1]))
		{
			if (!isset($headers[$h[0]]))
				$headers[$h[0]] = trim($h[1]);
			elseif (is_array($headers[$h[0]]))
			{
				// $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
				// $headers[$h[0]] = $tmp; // [-]
				$headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
			}
			else
			{
				// $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
				// $headers[$h[0]] = $tmp; // [-]
				$headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
			}

			$key = $h[0]; // [+]
		}
		else // [+]
		{ // [+]
			if (substr($h[0], 0, 1) == "\t") // [+]
				$headers[$key] .= "\r\n\t".trim($h[0]); // [+]
			elseif (!$key) // [+]
				$headers[0] = trim($h[0]);trim($h[0]); // [+]
		} // [+]
	}

	return $headers;
}


// Set up

$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION,	1); 
curl_setopt ($ch, CURLOPT_HEADER,		  1);  

// timeout (seconds)
curl_setopt ($ch, CURLOPT_TIMEOUT, 120);

// header

$headers = array(
	'Authorization: Bearer ' . $token,
	'Accept: application/vnd.mendeley-document.1+json'
);
	
curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);

// Go
$first = true;
$done = false;
while (!$done)
{
	if ($first)
	{
		$parameters = array(
			'group_id' => $group_id,
			//'view'		=> 'tags',
			'view'		=> 'all',
			'limit'		=> 100
		);
	
		$url = 'https://api.mendeley.com/documents?' . http_build_query($parameters);
		
		$first = false;
	}	
	
	echo $url . "\n";


	curl_setopt ($ch, CURLOPT_URL, $url); 
				
	$curl_result = curl_exec ($ch); 
		
	if (curl_errno ($ch) != 0 )
	{
		echo "CURL error: ", curl_errno ($ch), " ", curl_error($ch);
	}
	else
	{
		$info = curl_getinfo($ch);
		
		//print_r($info);
		
		$http_code = $info['http_code'];
		
		if ($http_code == 200)
		{
			// data
			$data = substr($curl_result, $info['header_size']);
		
			// Do something with data
			//echo $data;			
			
			$doc = new stdclass;
			$doc->_id = $url;
			$doc->status = "ok";
			
			$doc->{'message-format'} = $info['content_type'];
			
			$doc->message = json_decode($data);
			
			$couch->add_update_or_delete_document($doc, $doc->_id);
			
		
			// Any more?
		 
			$header = substr($curl_result, 0, $info['header_size']);
			//echo $header;
		
			$response_headers = http_parse_headers($header);
			
			$done = true;
			if (isset($response_headers['Link']))
			{
				foreach ($response_headers['Link'] as $link)
				{
					if (preg_match('/<(?<url>.*)>; rel="next"/', $link, $m))
					{
						$url = $m['url'];
						$done = false;
					}
				}
			}
		}
		else
		{
			$done = true;
		}
	}
}

	
?>