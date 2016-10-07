<?php

// Fetch reference from PubMed
require_once(dirname(dirname(__FILE__)) . '/lib.php');

//require_once (dirname(dirname(__FILE__)) . '/ncbi/fetch.php');


//----------------------------------------------------------------------------------------
function pubmed_parse_xml($xml)
{
	$dom = new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);
	
	$reference = new stdclass;
	
	$reference->{'message-format'} = 'application/vnd.crossref-api-message+json';
	
	$reference->message = new stdclass;
	
	// PMID is identifier
	$nodeCollection = $xpath->query ('//PubmedArticle/MedlineCitation/PMID');
	foreach ($nodeCollection as $node)
	{		
		$reference->message->pmid = $node->firstChild->nodeValue;
	}

	// title
	$nodeCollection = $xpath->query ('//ArticleTitle');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->title[] = $node->firstChild->nodeValue;
	}

	// abstract
	$nodeCollection = $xpath->query ('//Abstract/AbstractText');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->abstract = $node->firstChild->nodeValue;
	}
            
	// Pagination
	$nodeCollection = $xpath->query ('//Pagination/MedlinePgn');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->page = $node->firstChild->nodeValue;
		
		if (preg_match('/(?<spage>\d+)-(?<epage>\d+)/', $reference->message->page, $m))
		{
			$length_spage = strlen($m['spage']);
			$length_epage = strlen($m['epage']);
			
			if ($length_spage > $length_epage)
			{
				$pageStart = $m['spage'];
				$pageEnd = substr($m['spage'], 0, ($length_spage - $length_epage)) . $m['epage'];
				$reference->message->page = $pageStart . '-' . $pageEnd;
			}
		}
		
	}

	$nodeCollection = $xpath->query ('//Journal');
	foreach ($nodeCollection as $journal_node)
	{	
		$reference->type =  "journal-article";
	
		$nc = $xpath->query ('Title', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->{'container-title'}[] = $n->firstChild->nodeValue;
		}
					
		$nc = $xpath->query ('JournalIssue/Volume', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->volume =  $n->firstChild->nodeValue;
		}
		$nc = $xpath->query ('JournalIssue/Issue', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->issue =  $n->firstChild->nodeValue;
		}

		$nc = $xpath->query ('ISSN[@IssnType="Print"]', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->ISSN[] = $n->firstChild->nodeValue;
		}
		$nc = $xpath->query ('ISSN[@IssnType="Electronic"]', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->ISSN[] = $n->firstChild->nodeValue;
		}
		
		// date
		$nc = $xpath->query ('JournalIssue/PubDate/Year', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->issued['date-parts'][0][] = (Integer)$n->firstChild->nodeValue;
		}
		$nc = $xpath->query ('JournalIssue/PubDate/Month', $journal_node);
		foreach ($nc as $n)
		{	
			$months = array(
				'Jan'=>1,
				'Feb'=>2,
				'Mar'=>3,
				'Apr'=>4,
				'May'=>5,
				'Jun'=>6,
				'Jul'=>7,
				'Aug'=>8,
				'Sep'=>9,
				'Oct'=>10,
				'Nov'=>11,
				'Dec'=>12);
		
			$reference->message->issued['date-parts'][0][] = $months[$n->firstChild->nodeValue];
		}
		$nc = $xpath->query ('JournalIssue/PubDate/Day', $journal_node);
		foreach ($nc as $n)
		{	
			$reference->message->issued['date-parts'][0][] = (Integer)$n->firstChild->nodeValue;
		}
				
	}
	
	$reference->message->author = array();
	
	// authors
	$nodeCollection = $xpath->query ('//AuthorList/Author');
	foreach ($nodeCollection as $node)
	{	
		$author = new stdclass;

		$nc = $xpath->query ('ForeName', $node);
		foreach ($nc as $n)
		{	
			$author->given = $n->firstChild->nodeValue;
		}
		if (!isset($author->given))
		{
			$nc = $xpath->query ('Initials', $node);
			foreach ($nc as $n)
			{	
				$author->givenName = $n->firstChild->nodeValue;
			}
		}
		$nc = $xpath->query ('LastName', $node);
		foreach ($nc as $n)
		{	
			$author->family = $n->firstChild->nodeValue;
		}
		
		// Use address for affiliation as affiliation is a schema.org type that expects
		// a class, not text.
		$nc = $xpath->query ('AffiliationInfo/Affiliation', $node);
		foreach ($nc as $n)
		{	
			$author->affiliation[] = $n->firstChild->nodeValue;
		}
		
		
		$reference->message->author[] = $author;		
	}		
	
	// identifiers
	$nodeCollection = $xpath->query ('//ArticleIdList/ArticleId[@IdType = "doi"]');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->DOI = $node->firstChild->nodeValue;
	}
	$nodeCollection = $xpath->query ('//ArticleIdList/ArticleId[@IdType = "pmc"]');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->pmc = $node->firstChild->nodeValue;
	}
	
	/*
	// citations
	$reference->message->cited_by = pmid_cite($reference->message->pmid);
	
	if (isset($reference->message->pmc))
	{
		$reference->message->cites = pmc_cites_in_pubmed($reference->message->pmc);	
		$reference->message->cited_by = pmc_cited_by_pmc($reference->message->pmc);
	}
	*/
	
	/*
	// mesh	
	$nodeCollection = $xpath->query ('//MeshHeadingList/MeshHeading/DescriptorName/@UI');
	foreach ($nodeCollection as $node)
	{	
		$reference->message->mesh[] = $node->firstChild->nodeValue;
	}
	*/
	
	/*
	if (isset($reference->message->pmc))
	{
		cites_in_pubmed($reference->message->pmc, $reference);
		cited_by_in_pmc($reference->message->pmc, $reference);
	}
	*/
	
	//$reference->message->sequences = pubmed_to_nucleotides($reference->message->pmid);
	//print_r($reference);
	
	
	//echo json_format(json_encode($reference));
	
	return $reference;

}

//----------------------------------------------------------------------------------------
function pubmed_fetch($pmid)
{
	$data = null;
	//$xml = file_get_contents('17148433.xml');
	//$data = pubmed_parse_xml($xml);
	
	if (1)
	{
		// Eutils XML		
		$parameters = array(
			'db'		=> 'pubmed',
			'id' 		=> $pmid,
			'retmode'	=> 'xml'
		);
	
		$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' . http_build_query($parameters);
		
		//echo $url . "\n";
	
		$xml = get($url);
		
		//echo $xml;
		
		if ($xml != '')
		{
			$data = pubmed_parse_xml($xml);
		}
	}
	
	return $data;

}
	
if (0)
{	
	$pmid = 21605690;
	$pmid = 21653447;
	$pmid = 24315868;
	$data = pubmed_fetch($pmid);
	print_r($data);
}





?>
