<?php

// Test DwCA reader


require_once(dirname(dirname(dirname(__FILE__))) . '/documentstore/couchsimple.php');

require_once(dirname(__FILE__) . '/dwca.php');

/*
//--------------------------------------------------------------------------------------------------
function data_display($data)
{
	if (isset($data->_id))
	{
		$filename = 'tmp/' . urlencode($data->_id) . '.json';
		file_put_contents($filename, json_format(json_encode($data)) );
	}
	else
	{
		// extension
		$filename = 'tmp/' . urlencode($data->_coreid) . '.json';
		if (file_exists($filename))
		{
		
			unset($data->_coreid);
			
			$json = file_get_contents($filename);
			$obj = json_decode($json);
			
			switch ($data->_type)
			{
				case "Description":
					if (!isset($obj->Description))
					{
						$obj->Description = array();
					}
					$obj->Description[] = $data;
					break;
			
				case "Distribution":
					if (!isset($obj->Distribution))
					{
						$obj->Distribution = array();
					}
					$obj->Distribution[] = $data;
					break;

				case "Document":
					if (!isset($obj->Document))
					{
						$obj->Document = array();
					}
					$obj->Document[] = $data;
					break;
			
				case "Identifier":
					if (!isset($obj->Identifier))
					{
						$obj->Identifier = array();
					}
					$obj->Identifier[] = $data;
					break;

				case "Identification":
					if (!isset($obj->Identification))
					{
						$obj->Identification = array();
					}
					$obj->Identification[] = $data;
					break;
					
				case "Image":
					if (!isset($obj->Image))
					{
						$obj->Image = array();
					}
					$obj->Image[] = $data;
					break;
					
				case "Media":
					if (!isset($obj->Media))
					{
						$obj->Media = array();
					}
					$obj->Media[] = $data;
					break;					

				case "Occurrence":
					if (!isset($obj->Occurrence))
					{
						$obj->Occurrence = array();
					}
					$obj->Occurrence[] = $data;
					break;

				case "Reference":
					if (!isset($obj->Reference))
					{
						$obj->Reference = array();
					}
					$obj->Reference[] = $data;
					break;
					
				case "SpeciesProfile":
					if (!isset($obj->SpeciesProfile))
					{
						$obj->SpeciesProfile = array();
					}
					$obj->SpeciesProfile[] = $data;
					break;	

				case "TypesAndSpecimen":
					if (!isset($obj->TypesAndSpecimen))
					{
						$obj->TypesAndSpecimen = array();
					}
					$obj->TypesAndSpecimen[] = $data;
					break;
					
				case 'VernacularName':
					// Many DWCA seem to have blank values for vernacular names
					if (isset($data->vernacularName))
					{
						if (!isset($obj->VernacularName))
						{
							$obj->VernacularName = array();
						}
						$obj->VernacularName[] = $data;
					}
					break;
					
				default:
					break;
			}
			file_put_contents($filename, json_format(json_encode($obj)) );
		}
	}		


	echo json_format(json_encode($data)) . "\n";
}
*/

//--------------------------------------------------------------------------------------------------
function data_store_couchdb($data)
{
	global $couch;
	
	if (isset($data->_id))
	{
		switch ($data->_type)
		{
			// We can handle occurrences
			case "Occurrence":				
				// message
				$doc = new stdclass;
				$doc->_id = 'http://www.gbif.org/occurrence/' . $data->_id;
				
				// clean up data fields we don't need 
				unset($data->_id);
				unset($data->_type);
				
				$doc->{'message-timestamp'} = date("c", time());
				$doc->{'message-modified'} 	= $doc->{'message-timestamp'};
				$doc->{'message-format'} 	= 'gbif-occurrence';
				
				$doc->message = $data;
				
				//$couch->add_update_or_delete_document(null, $doc->_id, 'delete');
				
				$couch->add_update_or_delete_document($doc, $doc->_id);
				break;
				
			default:
				break;
		}
	}
	else
	{
		// extension, so add to existing data object in CouchDB
		// to do
	}
}


//--------------------------------------------------------------------------------------------------

// Archive to parse
$basedir = '0028114-160910150852091/';

// meta.xml tells us how to interpret archive
$filename = $basedir . 'meta.xml';
$xml = file_get_contents($filename);

// Read details of source file(s) and extract data
$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('dwc_text', 'http://rs.tdwg.org/dwc/text/');

//parse_eml($xpath);

// set a custom function to determine how we post-process the data
parse_meta($xpath, '//dwc_text:core', 'data_store_couchdb');
parse_meta($xpath, '//dwc_text:extension', 'data_store_couchdb');

//print_r($geometry);

//echo json_encode($geometry);

?>
