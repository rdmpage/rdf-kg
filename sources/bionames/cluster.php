<?php

$json = '{
  "_id": "cluster\/2149330",
  "_rev": "4-41ae7a26f9e5be08e98ea841f1790bc4",
  "type": "nameCluster",
  "names": [
    {
      "nomenclaturalCode": "ICZN",
      "id": "urn:lsid:organismnames.com:name:3771429",
      "nameComplete": "Anostomus spiloclistron",
      "genusPart": "Anostomus",
      "specificEpithet": "spiloclistron",
      "rankString": "species",
      "group": [
        "Animalia",
        "Chordata",
        "Vertebrata",
        "Pisces",
        "Osteichthyes",
        "Actinopterygii",
        "Characiformes",
        "Curimatidae"
      ]
    },
    {
      "nomenclaturalCode": "ICZN",
      "id": "urn:lsid:organismnames.com:name:2149330",
      "nameComplete": "Anostomus spiloclistron",
      "taxonAuthor": "Winterbottom 1974",
      "genusPart": "Anostomus",
      "specificEpithet": "spiloclistron",
      "rankString": "species",
      "publication": "Winterbottom 1974. A new species of anostomid characoid fish, Anostomus spilochistron, from the Nickerie River System of western Surinam (Pisces, Cypriniformes, Anostomidae). Beaufortia, 21(283) 1974: 153-163",
      "year": "1974",
      "publishedInCitation": "c313110b32072625f07048dd0f01592e",
      "microreference": "154",
      "group": [
        "Animalia",
        "Chordata",
        "Vertebrata",
        "Pisces",
        "Osteichthyes",
        "Actinopterygii",
        "Characiformes",
        "Curimatidae"
      ]
    }
  ],
  "nomenclaturalCode": "ICZN",
  "publication": [
    "Winterbottom 1974. A new species of anostomid characoid fish, Anostomus spilochistron, from the Nickerie River System of western Surinam (Pisces, Cypriniformes, Anostomidae). Beaufortia, 21(283) 1974: 153-163"
  ],
  "publishedInCitation": [
    "c313110b32072625f07048dd0f01592e"
  ],
  "rankString": "species",
  "taxonAuthor": "Winterbottom 1974",
  "year": [
    "1974"
  ],
  "nameComplete": "Anostomus spiloclistron",
  "genusPart": "Anostomus",
  "specificEpithet": "spiloclistron",
  "group": [
    "Animalia",
    "Chordata",
    "Vertebrata",
    "Pisces",
    "Osteichthyes",
    "Actinopterygii",
    "Characiformes",
    "Curimatidae"
  ],
  "microreference": [
    "154"
  ],
  "status": 200
}';


$obj = json_decode($json);

// convert to JSON-LD

$data = new stdclass;

$data->{'@context'} = new stdclass;
$data->{'@context'}->{'@vocab'} = 'http://schema.org/';
$data->{'@context'}->dwc = 'http://rs.tdwg.org/dwc/terms/';

$data->{'@context'}->co = 'http://rs.tdwg.org/ontology/voc/Common#';
$data->{'@context'}->tn = 'http://rs.tdwg.org/ontology/voc/TaxonName#'; 

$data->{'@id'} = 'http://bionames.org/' . $obj->_id;

$data->{'@type'} = 'DataFeed';
$data->dataFeedElement = array();

foreach ($obj->names as $name)
{
	$item = new stdclass;
	
	$item->{'@id'} = $name->id;
	$item->{'@type'} = 'tn:TaxonName';
	
	
	$lsid = new stdclass;
	$lsid->{'@id'} = $name->id;
	$item->{'dc:identifier'}[] = $lsid;
	
	$item->url = 'http://www.organismnames.com/namedetails.htm?lsid=' . str_replace('urn:lsid:organismnames.com:name:', '', $name->id);
	
	/*
	$item->sameAs[] = 'http://www.organismnames.com/namedetails.htm?lsid=' . str_replace('urn:lsid:organismnames.com:name:', '', $name->id);
	*/
	
	$item->name = $name->nameComplete;
	
	// TaxonName
	$keys = array(
		"nameComplete",
  		"uninomial",
  		"genusPart",
  		"infragenericEpithet",
  		"specificEpithet",
		"infraspecificEpithet",
		"rankString",
  		"taxonAuthor",
  		"nomenclaturalCode"
  	);
  	
  	foreach ($keys as $key)
  	{
  		if (isset($name->{$key}))
  		{
  			$item->{'tn:' . $key} = $name->{$key};
  		}
  	}
  	
  	// Common
  	if (isset($name->publication))
  	{
  		$item->{'co:publishedIn'} = $name->publication;
  	}
  	
  	
  	if (isset($name->publishedInCitation))
  	{
  		$publishedInCitation = new stdclass;
  		$publishedInCitation->{'@id'} = 'http://bionames.org/references/' . $name->publishedInCitation;
  		$item->{'co:publishedInCitation'}[] = $publishedInCitation;
  	}
  	
  	if (isset($name->microreference))
  	{
  		$item->{'co:microreference'} = $name->microreference;
  	}
  		

	

	$data->dataFeedElement[] = $item;
}



print_r($data);

echo json_encode($data);





?>