<?php

// Fetch DNA barcode
// Fetch sequence(s) from GenBank and convert to JSON
// Fetch reference from PubMed
require_once(dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
function barcode_fetch($barcode)
{
	$obj = null;
	
	// API call
	
	$parameters = array(
		'ids' 	=> $barcode,
		'format'	=> 'tsv'
	);
	
	$url = 'http://www.boldsystems.org/index.php/API_Public/combined?' . http_build_query($parameters);
	
	echo $url;
	
	$data = get($url);
	
	//echo $data;
	
	if ($data != '')
	{

		$lines = explode("\n", $data);

		$keys = array();
		$row_count = 0;
	
		foreach ($lines as $line)
		{
		
			if ($line == '') break;
			$row = explode("\t", $line);
		
			if ($row_count == 0)
			{
				$keys = $row;
			}
			else
			{
				$obj = new stdclass;
				$obj->{'message-format'} = 'text/tab-separated-values';
				$obj->message = new stdclass;
				$obj->links = array();
			
				$n = count($row);
				for ($i = 0; $i < $n; $i++)
				{
					if (trim($row[$i]) != '')
					{
						$obj->message->{$keys[$i]} = $row[$i];
						
						// store links
						switch ($keys[$i])
						{
							case 'genbank_accession':
								$obj->links[] = 'http://www.ncbi.nlm.nih.gov/nuccore/' . $row[$i];
								break;
							case 'bin_uri':
								$obj->links[] = 'http://bins.boldsystems.org/index.php/Public_BarcodeCluster?clusteruri=' . $row[$i];
								break;
							default:
								break;
						}
					}
				}
			}
			$row_count++;
		}

	}
	return $obj;
}
	
if (0)
{
	$bold = 'USNMD174-11';
	$bold = 'BZLWB111-06';
	$bold = 'ASANQ054-09'; // image
	//$bold = 'MHMYC1083-09'; // 2 images
	
	//$bold = 'BZLWE029-08';
	
	$obj = barcode_fetch($bold);
	print_r($obj);

	echo json_encode($obj);
}

?>
