<?php

// GBIF

require_once(dirname(dirname(__FILE__)) . '/lib.php');


//----------------------------------------------------------------------------------------
// GBIF API
function get_occurrence($id)
{
	$data = null;
	
	$url = 'http://api.gbif.org/v1/occurrence/' . $id;

	$json = get($url);
	
	if ($json != '')
	{
		$obj = json_decode($json);
		if ($obj)
		{
			$data = new stdclass;
			$data->{'message-format'} = 'gbif-occurrence';
			$data->message = $obj;
			
			// links
			$data->links = array();
			
			// sequences------------------------------------------------------------------
			$genbank_list = array();
			
			// Listed in GBIF record
			if (isset($obj->associatedSequences))
			{
				$genbank = $obj->associatedSequences;
				$genbank = preg_replace('/Genbank:/i', '', $genbank);
				$genbank = preg_replace('/\s+/', '', $genbank);
				$genbank = preg_replace('/http:\/\/www.ncbi.nlm.nih.gov\/nuccore/', '', $genbank);
			
				$genbank = preg_replace('/\s*;\s*/', '|', $genbank);
				
				if ($genbank != '')
				{
					$genbank_list = explode('|', $genbank);
				}
			}
			
			// EMBL (and other datasets) that have GenBank accession numbers as identifiers
			// to extract sequence accession numbers
			if ($obj->datasetKey == 'c1fc2df7-223b-4472-8998-70afb3b749ab')
			{
				$genbank_list[] = $obj->catalogNumber;
			}
			
			$genbank_list = array_unique($genbank_list);
			
			foreach ($genbank_list as $accession)
			{
				$data->links[] = 'http://www.ncbi.nlm.nih.gov/nuccore/' . $accession;
			}
			
			// collection-----------------------------------------------------------------
			if (isset($obj->collectionID))
			{
				if (preg_match('/^(http|urn)/', $obj->collectionID))
				{
					$data->links[] = $obj->collectionID;
				}
			}
			

			// taxon----------------------------------------------------------------------
			if (isset($obj->taxonKey))
			{
				$data->links[] = 'http://www.gbif.org/species/' . $obj->taxonKey;
			}
			
									
		}
	}
	
	return $data;
}

//----------------------------------------------------------------------------------------
// GBIF API
function get_gbif_species($id)
{
	$data = null;
	
	$url = 'http://api.gbif.org/v1/species/' . $id;

	$json = get($url);
	
	if ($json != '')
	{
		$obj = json_decode($json);
		if ($obj)
		{
			$data = new stdclass;
			$data->{'message-format'} = 'gbif-species';
			$data->message = $obj;
			
			$data->links = array();
			
			// synonyms, references, types, taxon names, etc.?			
									
		}
	}
	
	return $data;
}

//----------------------------------------------------------------------------------------
function gbif_fetch_occurrence($id)
{
	$data = get_occurrence($id);
	return $data;
}

//----------------------------------------------------------------------------------------
function gbif_fetch_species($id)
{
	$data = get_gbif_species($id);
	return $data;
}


// test cases

if (0)
{
	$id = 3257628;
	
	$data = gbif_fetch_species($id);
	
	print_r($data);
	
	echo json_encode($data);
	
	
}

if (0)
{
	$id = 1249286565;
	$id = 1004022935; // EMBL 
	$id = 886683894; // deleted :(
	$id = 888965011; // deleted :(
	$id = 1211933420;
	$id = 317834729;
	$id = 668534424;
	$data = gbif_fetch_occurrence($id);
	
	print_r($data);
	
	echo json_encode($data);
}

?>
