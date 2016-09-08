<?php

// look up from DOI

require_once(dirname(__FILE__) . '/lib.php');

$dois = array(
'10.1111/syen.12096'
);

foreach ($dois as $doi)
{
	//echo '.';
	
	$url = 'http://pub.orcid.org/v1.2/search/orcid-bio/'; 
	
	$url = $url . '?' . http_build_query( array('q' => 'digital-object-ids:"' . $doi . '"'));
	
	$json = get($url, '', 'application/json');
	
	//echo $json;
	
	$obj = json_decode($json);
	
	//print_r($obj);
	
	
	foreach ($obj->{'orcid-search-results'}->{'orcid-search-result'} as $result)
	{
		$orcid = $result->{'orcid-profile'}->{'orcid-identifier'}->{'path'};
		$name = $result->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'given-names'}->{'value'}
			. ' ' . $result->{'orcid-profile'}->{'orcid-bio'}->{'personal-details'}->{'family-name'}->{'value'};
		echo  $doi . "\t" . $orcid . "\t" . $name . "\n";
	}
	
	// authors
	
	// source
}


?>