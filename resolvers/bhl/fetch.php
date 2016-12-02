<?php

// BHL

require_once(dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
function get_bhl_page($PageID)
{
	$data = null;
	
	$parameters = array(
		'op' => 'GetPageMetadata',
		'pageid' => $PageID,
		'ocr' => 'true',
		'names' => 'true',
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);
	
	$url = 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);
		
	$json = get($url);
	
	if ($json != '')
	{
		$obj = json_decode($json);
		if ($obj)
		{
			if ($obj->Status == 'ok')
			{
				$data = new stdclass;
				$data->{'message-format'} = 'bhl-page';
				
				$data->message = $obj->Result;
			}
		}
	}
	
	return $data;
}

//----------------------------------------------------------------------------------------
function bhl_page_fetch($PageID)
{
	$data = get_bhl_page($PageID);
	return $data;
}



if (0)
{
	$PageID = 15775524;
	
	$data = bhl_page_fetch($PageID);
	
	print_r($data);
}	



?>
