<?php

// ZooBank

require_once(dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
// GBIF API
function zoobank_get_reference($uuid)
{
	$data = null;
	
	$url = 'http://zoobank.org/References.json/' . $uuid;
	
	$json = get($url);
		
	if ($json != '')
	{
		$obj = json_decode($json);
		if ($obj)
		{
			$data = new stdclass;
			$data->{'message-format'} = 'zoobank-reference';
			$data->message = $obj;
			
			// links
			$data->links = array();
			
			
			// identifiers
			$url = 'http://zoobank.org/Identifiers.json/' . $uuid;
			$json = get($url);
		
			if ($json != '')
			{
				$identifiers = json_decode($json);
				if ($identifiers)
				{
					$data->message[] = $identifiers;
					
					foreach ($identifiers as $identifier)
					{
						if ($identifier->IdentifierDomain == 'Digital Object Identifier')
						{
							$doi = 'http://dx.doi.org/' . $identifier->IdentifierURL;
							if (!in_array($doi, $data->links))
							{
								$data->links[] = $identifier->IdentifierURL;
							}
						}
					}
				}
			}
			
			

			
									
		}
	}
	
	return $data;
}


//----------------------------------------------------------------------------------------
function zoobank_fetch_reference($uuid)
{
	$data = zoobank_get_reference($uuid);
	return $data;
}


// test cases

if (1)
{
	$uuid = '664344e4-fa3f-4f12-a1ee-83b95bfe09af';
	$uuid = '38457a27-a15b-4e87-be72-9a3ab007caa5';
	$data = zoobank_fetch_reference($uuid);
	
	print_r($data);
	
	//echo json_encode($data);
}

?>
