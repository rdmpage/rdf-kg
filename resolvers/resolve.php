<?php

// Resolve one object
require_once(dirname(dirname(__FILE__)) . '/documentstore/couchsimple.php');

require_once (dirname(__FILE__) . '/bold/fetch.php');
require_once (dirname(__FILE__) . '/crossref/fetch.php');
require_once (dirname(__FILE__) . '/gbif/fetch.php');
require_once (dirname(__FILE__) . '/genbank/fetch.php');
require_once (dirname(__FILE__) . '/orcid/fetch.php');
require_once (dirname(__FILE__) . '/pubmed/fetch.php');
require_once (dirname(__FILE__) . '/worldcat/fetch.php');

//----------------------------------------------------------------------------------------
// Classify URL link
function classify_url($url)
{
	$identifier = null;
	
	// BOLD
	if (preg_match('/http[s]?:\/\/bins.boldsystems.org\/index.php\/Public_RecordView\?processid=(?<id>.*)$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'BOLD';
		$identifier->id = $m['id'];
	}
		
	// DOI
	if (preg_match('/http[s]?:\/\/(dx.)?doi.org\/(?<doi>.*)$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'DOI';
		$identifier->id = $m['doi'];
	}

	// GBIF Occurrence
	if (preg_match('/http[s]?:\/\/(www\.)?gbif.org\/occurrence\/(?<id>\d+)$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'GBIF_OCCURRENCE';
		$identifier->id = $m['id'];
	}
	
	// ORCID
	if (preg_match('/http[s]?:\/\/orcid.org\/(?<orcid>([0-9]{4})(-[0-9A-Z]{4}){3})$/i', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'ORCID';
		$identifier->id =  $m['orcid'];
	}
	
	// ISSN (WorldCat)
	if (preg_match('/http[s]?:\/\/www.worldcat.org\/issn\/(?<issn>[0-9]{4}-[0-9]{3}([0-9]|X))$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'ISSN';
		$identifier->id = $m['issn'];
	}	

	// NCBI GenBank
	if (preg_match('/http[s]?:\/\/www.ncbi.nlm.nih.gov\/nuccore\/(?<id>.*)$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'GENBANK';
		$identifier->id = $m['id'];
	}
	
	// PubMed PMID
	if (preg_match('/http[s]?:\/\/www.ncbi.nlm.nih.gov\/pubmed\/(?<pmid>\d+)$/', $url, $m))
	{
		$identifier = new stdclass;
		$identifier->namespace = 'PMID';
		$identifier->id = $m['pmid'];
	}
	
	return $identifier;
}
	
	
//----------------------------------------------------------------------------------------
function resolve_url($url)
{
	$data = null;
	
	$identifier = classify_url($url);

	if ($identifier)
	{	
		switch ($identifier->namespace)
		{	
			case 'BOLD':
				$data = barcode_fetch($identifier->id);
				break;
		
			case 'DOI':
				$data = crossref_fetch($identifier->id);
				break;
				
			case 'GBIF_OCCURRENCE':
				$data = gbif_fetch_occurrence($identifier->id);
				break;

			case 'GENBANK':
				$data = genbank_fetch($identifier->id);
				break;
				
			case 'ISSN':
				$data = worldcat_fetch($identifier->id);
				break;

			case 'ORCID':
				$data = orcid_fetch($identifier->id);
				break;
		
			case 'PMID':
				$data = pubmed_fetch($identifier->id);
				break;
			
			default:
				break;
		}
	}
	return $data;
}

// test
if (0)
{
	$url = 'http://dx.doi.org/10.1080/00222934908526725';
	
	//$url = 'http://www.worldcat.org/issn/0075-5036';
	
	//$url = 'http://www.worldcat.org/issn/1313-2970';
	
	$url = 'http://www.ncbi.nlm.nih.gov/pubmed/27058864';
	
	$url = 'http://bins.boldsystems.org/index.php/Public_RecordView?processid=ASANQ054-09';
	
	$url = 'http://www.ncbi.nlm.nih.gov/nuccore/146428523';
	
	$data = resolve_url($url);
	print_r($data);

}

?>