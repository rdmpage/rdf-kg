<?php

// WorldCat to get data about ISSN

require_once (dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
function worldcat_fetch($issn)
{
	$data = null;
	
	$url = 'http://xissn.worldcat.org/webservices/xid/issn/' . $issn . '?method=getHistory&format=json';
	
	$json = get($url);
	if ($json != '')
	{
		$data = new stdclass;
		$data->{'message-format'} = 'application/json';		
		$data->links = array();
		
		//echo $json;
		
		$obj = json_decode($json);
		
		$preceding = array();
		$succeeding = array();
		
		
		// Get relations between journals		
		if (isset($obj->group))
		{
			foreach ($obj->group as $g)
			{
				foreach ($g->list as $list)
				{
					switch ($g->rel)
					{							
						case 'preceding':
							$preceding[] = $list->issn;
							break;							
						case 'succeeding':
							$succeeding[] = $list->issn;
							break;							
						case 'this':
							if (($list->issnl == $issn) || ($list->issn == $issn))
							{						
								$data->message = $list;
							}
							break;
						default:
							break;
					}
				}
			}
		}
		
		// clean
		$preceding = array_unique($preceding);
		$succeeding = array_unique($succeeding);

		$data->message->preceding = $preceding;
		$data->message->succeeding = $succeeding;
		
		// clean
		foreach($preceding as $issn)
		{
			$data->links[] = 'http://www.worldcat.org/issn/' . $issn;
		}
		foreach($succeeding as $issn)
		{
			$data->links[] = 'http://www.worldcat.org/issn/' . $issn;
		}
	}
	return $data;
}

//----------------------------------------------------------------------------------------
if (0)
{
	$issn = '0187-7151';
	$issn = '0067-2238';
	//$issn = '1464-5262';
	//$issn = '1471-4922';
	//$issn = '0075-5036';
	//$issn = '0091-7958';
	
	$data = worldcat_fetch($issn);
	
	print_r($data);
	
	echo json_encode($data);
}

	

?>