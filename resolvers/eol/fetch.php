<?php

// EOL

require_once(dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
// EOL taxon page
function eol_taxon($id)
{
	$data = null;
	
	$url = 'http://eol.org/api/pages/1.0/' . $id . '.json?details=1&amp;common_names=1&amp;images=10';
	
	$json = get($url);
	
	if ($json != '')
	{
		$obj = json_decode($json);
		if ($obj)
		{
			$data = new stdclass;
			$data->{'message-format'} = 'eol-taxon';
			$data->message = $obj;
			
			// links
			
			// traitbank?
			$data->links = array();



									
		}
	}
	
	return $data;
}



// test cases

if (1)
{
	$id = 10692652;
	$id = 328067;
	$id = 24759965;
	$data = eol_taxon($id);
	
	print_r($data);
	
	echo json_encode($data);
}

?>
