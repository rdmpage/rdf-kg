<?php

// Fetch sequence(s) from GenBank and convert to JSON
// Fetch reference from PubMed
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once(dirname(dirname(__FILE__)) . '/nameparse.php');

require_once (dirname(dirname(__FILE__)) . '/shared/crossref.php');
require_once (dirname(dirname(__FILE__)) . '/shared/ncbi.php');


// https://github.com/asonge/php-geohash
//require_once (dirname(dirname(dirname(__FILE__))) . '/vendor/geohash.php');


//----------------------------------------------------------------------------------------
/**
 * @brief Convert degrees, minutes, seconds to a decimal value
 *
 * @param degrees Degrees
 * @param minutes Minutes
 * @param seconds Seconds
 * @param hemisphere Hemisphere (optional)
 *
 * @result Decimal coordinates
 */
function degrees2decimal($degrees, $minutes=0, $seconds=0, $hemisphere='N')
{
	$result = $degrees;
	$result += $minutes/60.0;
	$result += $seconds/3600.0;
	
	if ($hemisphere == 'S')
	{
		$result *= -1.0;
	}
	if ($hemisphere == 'W')
	{
		$result *= -1.0;
	}
	// Spanish
	if ($hemisphere == 'O')
	{
		$result *= -1.0;
	}
	// Spainish OCR error
	if ($hemisphere == '0')
	{
		$result *= -1.0;
	}
	
	return $result;
}

//----------------------------------------------------------------------------------------
function process_lat_lon(&$locality, $lat_lon)
{

	$matched = false;

	if (preg_match ("/(N|S)[;|,] /", $lat_lon))
	{
		// it's a literal string description, not a pair of decimal coordinates.
		if (!$matched)
		{
			//  35deg12'07'' N; 83deg05'2'' W, e.g. DQ995039
			if (preg_match("/([0-9]{1,2})deg([0-9]{1,2})'(([0-9]{1,2})'')?\s*([S|N])[;|,]\s*([0-9]{1,3})deg([0-9]{1,2})'(([0-9]{1,2})'')?\s*([W|E])/", $lat_lon, $matches))
			{
				//print_r($matches);
			
				$degrees = $matches[1];
				$minutes = $matches[2];
				$seconds = $matches[4];
				$hemisphere = $matches[5];
				$lat = $degrees + ($minutes/60.0) + ($seconds/3600);
				if ($hemisphere == 'S') { $lat *= -1.0; };

				$locality->decimalLatitude = $lat;

				$degrees = $matches[6];
				$minutes = $matches[7];
				$seconds = $matches[9];
				$hemisphere = $matches[10];
				$long = $degrees + ($minutes/60.0) + ($seconds/3600);
				if ($hemisphere == 'W') { $long *= -1.0; };
				
				$locality->decimalLongitude = $long;
				
				$matched = true;
			}
		}
		if (!$matched)
		{
			
			list ($lat, $long) = explode ("; ", $lat_lon);

			list ($degrees, $rest) = explode (" ", $lat);
			list ($minutes, $rest) = explode ('.', $rest);

			list ($decimal_minutes, $hemisphere) = explode ("'", $rest);

			$lat = $degrees + ($minutes/60.0) + ($decimal_minutes/6000);
			if ($hemisphere == 'S') { $lat *= -1.0; };

			$locality->decimalLatitude = $lat;

			list ($degrees, $rest) = explode (" ", $long);
			list ($minutes, $rest) = explode ('.', $rest);

			list ($decimal_minutes, $hemisphere) = explode ("'", $rest);

			$long = $degrees + ($minutes/60.0) + ($decimal_minutes/6000);
			if ($hemisphere == 'W') { $long *= -1.0; };
			
			$locality->decimalLongitude = $long;
			
			$matched = true;
		}

	}
	
	if (!$matched)
	{			
		// N19.49048, W155.91167 [EF219364]
		if (preg_match ("/(?<lat_hemisphere>(N|S))(?<latitude>(\d+(\.\d+))), (?<long_hemisphere>(W|E))(?<longitude>(\d+(\.\d+)))/", $lat_lon, $matches))
		{
			$lat = $matches['latitude'];
			if ($matches['lat_hemisphere'] == 'S') { $lat *= -1.0; };
			
			$locality->decimalLatitude = $lat;
			
			$long = $matches['longitude'];
			if ($matches['long_hemisphere'] == 'W') { $long *= -1.0; };
			
			$locality->decimalLongitude = $long;
			
			$matched = true;

		}
	}
	
	if (!$matched)		
	{
		//13.2633 S 49.6033 E
		if (preg_match("/([0-9]+(\.[0-9]+)*) ([S|N]) ([0-9]+(\.[0-9]+)*) ([W|E])/", $lat_lon, $matches))
		{
			//print_r ($matches);
			
			$lat = $matches[1];
			if ($matches[3] == 'S') { $lat *= -1.0; };
			
			$locality->decimalLatitude = $lat;

			$long = $matches[4];
			if ($matches[6] == 'W') { $long *= -1.0; };
			
			$locality->decimalLongitude = $long;
			
			$matched = true;
		}
	}
	
	
	// AY249471 Palmer Archipelago 64deg51.0'S, 63deg34.0'W 
	if (!$matched)		
	{
		if (preg_match("/(?<lat_deg>[0-9]{1,2})deg(?<lat_min>[0-9]{1,2}(\.\d+)?)'\s*(?<lat_hemisphere>[S|N]),?\s*(?<long_deg>[0-9]{1,3})deg(?<long_min>[0-9]{1,2}(\.\d+)?)'\s*(?<long_hemisphere>[W|E])/", $lat_lon, $matches))
		{
			//print_r ($matches);
			
			$locality->decimalLatitude
				= degrees2decimal(
					$matches['lat_deg'], 
					$matches['lat_min'], 
					0,
					$matches['lat_hemisphere']
					);

			$locality->decimalLongitude
				= degrees2decimal(
					$matches['long_deg'], 
					$matches['long_min'], 
					0,
					$matches['long_hemisphere']
					);
			
			/*
			//exit();
			
			$lat = $matches[1];
			if ($matches[3] == 'S') { $lat *= -1.0; };
			$sequence->source->latitude = $lat;

			$long = $matches[4];
			if ($matches[6] == 'W') { $long *= -1.0; };
			
			$sequence->source->longitude = $long;
			*/
			
			//print_r($sequence);
			//exit();
			
			$matched = true;
		}
	}
	
	if (!$matched)
	{
		
		if (preg_match("/(?<latitude>\-?\d+(\.\d+)?),?\s*(?<longitude>\-?\d+(\.\d+)?)/", $lat_lon, $matches))
		{
			//print_r($matches);
			
			$locality->decimalLatitude  = $matches['latitude'];
			$locality->decimalLongitude = $matches['longitude'];
		
			$matched = true;
		}
	}
}

//----------------------------------------------------------------------------------------
function process_locality(&$locality)
{
	$debug = false;
		
	if (isset($locality->country))
	{
		$country = $locality->country;

		$matches = array();	
		$parts = explode (":", $country);	
		$locality->country = $parts[0];
		
		$locality_string = trim($parts[1]);
		
		if (count($parts) > 1)
		{
			$locality->locality = $locality_string;
			// Clean up
			$locality->locality = preg_replace('/\(?GPS/', '', $locality_string);				
		}	
		
		if ($debug)
		{
			echo "Trying line " . __LINE__ . "\n";
		}

		// Handle AMNH stuff
		if (preg_match('/(?<latitude_degrees>[0-9]+)deg(?<latitude_minutes>[0-9]{1,2})\'\s*(?<latitude_hemisphere>[N|S])/i', $locality_string, $matches))
		{
			if ($debug) { print_r($matches); }	

			$degrees = $matches['latitude_degrees'];
			$minutes = $matches['latitude_minutes'];
			$hemisphere = $matches['latitude_hemisphere'];
			$lat = $degrees + ($minutes/60.0);
			if ($hemisphere == 'S') { $lat *= -1.0; };

			$locality->decimalLatitude  = $lat;
		}
				

		if ($debug)
		{
			echo "Trying line " . __LINE__ . "\n";
		}
		if (preg_match('/(?<longitude_degrees>[0-9]+)deg(,\s*)?(?<longitude_minutes>[0-9]{1,2})\'\s*(?<longitude_hemisphere>[W|E])/i', $locality_string, $matches))
		{
		
			if ($debug) { print_r($matches); }	
			
			$degrees = $matches['longitude_degrees'];
			$minutes = $matches['longitude_minutes'];
			$hemisphere = $matches['longitude_hemisphere'];
			$long = $degrees + ($minutes/60.0);
			if ($hemisphere == 'W') { $long *= -1.0; };
			
			$locality->decimalLongitude  = $long;
		}
	
		if ($debug)
		{
			echo "Trying line " . __LINE__ . "\n";
		}

		if ($locality_string != '')
		{
			// AY249471 Palmer Archipelago 64deg51.0'S, 63deg34.0'W 
			if (preg_match("/(?<latitude_degrees>[0-9]{1,2})deg(?<latitude_minutes>[0-9]{1,2}(\.\d+)?)'\s*(?<latitude_hemisphere>[S|N]),\s*(?<longitude_degrees>[0-9]{1,3})deg(?<longitude_minutes>[0-9]{1,2}(\.\d+)?)'\s*(?<longitude_hemisphere>[W|E])/", $locality_string, $matches))
			{	
			
				if ($debug) { print_r($matches); }	

				$degrees = $matches['latitude_degrees'];
				$minutes = $matches['latitude_minutes'];
				$hemisphere = $matches['latitude_hemisphere'];
				$lat = $degrees + ($minutes/60.0);
				if ($hemisphere == 'S') { $lat *= -1.0; };

				$locality->decimalLatitude = $lat;

				$degrees = $matches['longitude_degrees'];
				$minutes = $matches['longitude_minutes'];
				$hemisphere = $matches['longitude_hemisphere'];
				$long = $degrees + ($minutes/60.0);
				if ($hemisphere == 'W') { $long *= -1.0; };
				
				$locality->decimalLongitude  = $long;
				
				$matched = true;
			}
			
			if (!$matched)
			{
				
				//26'11'24N 81'48'16W
				
				//echo $seq['source']['locality'] . "\n";
				
				if (preg_match("/
				(?<latitude_degrees>[0-9]{1,2})
				'
				(?<latitude_minutes>[0-9]{1,2})
				'
				((?<latitude_seconds>[0-9]{1,2})
				'?)?
				(?<latitude_hemisphere>[S|N])
				\s+
				(?<longitude_degrees>[0-9]{1,3})
				'
				(?<longitude_minutes>[0-9]{1,2})
				'
				((?<longtitude_seconds>[0-9]{1,2})
				'?)?
				(?<longitude_hemisphere>[W|E])
				/x", $locality_string, $matches))
				{
					if ($debug) { print_r($matches); }	
						
					$degrees = $matches['latitude_degrees'];
					$minutes = $matches['latitude_minutes'];
					$seconds = $matches['latitude_seconds'];
					$hemisphere = $matches['latitude_hemisphere'];
					$lat = $degrees + ($minutes/60.0) + ($seconds/3600);
					if ($hemisphere == 'S') { $lat *= -1.0; };
	
					$locality->decimalLatitude = $lat;
	
					$degrees = $matches['longitude_degrees'];
					$minutes = $matches['longitude_minutes'];
					$seconds = $matches['longtitude_seconds'];
					$hemisphere = $matches['longitude_hemisphere'];
					$long = $degrees + ($minutes/60.0) + ($seconds/3600);
					if ($hemisphere == 'W') { $long *= -1.0; };
					
					$locality->decimalLongitude = $long;
					
					//print_r($seq);
					
					//exit();
					
					$matched = true;
				}
			}
			//exit();

			
		}
		
		if ($debug)
		{
			echo "Trying line " . __LINE__ . "\n";
		}
		
		
		//(GPS: 33 38' 07'', 146 33' 12'') e.g. AY281244
		if (preg_match("/\(GPS:\s*([0-9]{1,2})\s*([0-9]{1,2})'\s*([0-9]{1,2})'',\s*([0-9]{1,3})\s*([0-9]{1,2})'\s*([0-9]{1,2})''\)/", $country, $matches))
		{
			if ($debug) { print_r($matches); }	
			
			$lat = $matches[1] + $matches[2]/60 + $matches[3]/3600;
			
			// OMG
			if ($seq['source']['country'] == 'Australia')
			{
				$lat *= -1.0;
			}
			$long = $matches[4] + $matches[5]/60 + $matches[6]/3600;

			$locality->decimalLatitude  = $lat;
			$locality->decimalLongitude  = $long;
			
		}
		
		if ($debug)
		{
			echo "Trying line " . __LINE__ . "\n";
		}
		
		
		// AJ556909
		// 2o54'59''N 98o38'24''E			
		if (preg_match("/
			(?<latitude_degrees>[0-9]{1,2})
			o
			(?<latitude_minutes>[0-9]{1,2})
			'
			(?<latitude_seconds>[0-9]{1,2})
			''
			(?<latitude_hemisphere>[S|N])
			\s+
			(?<longitude_degrees>[0-9]{1,3})
			o
			(?<longitude_minutes>[0-9]{1,2})
			'
			(?<longtitude_seconds>[0-9]{1,2})
			''
			(?<longitude_hemisphere>[W|E])
			/x", $locality_string, $matches))
		{
			if ($debug) { print_r($matches); }	
				
			$degrees = $matches['latitude_degrees'];
			$minutes = $matches['latitude_minutes'];
			$seconds = $matches['latitude_seconds'];
			$hemisphere = $matches['latitude_hemisphere'];
			$lat = $degrees + ($minutes/60.0) + ($seconds/3600);
			if ($hemisphere == 'S') { $lat *= -1.0; };

			$locality->decimalLatitude = $lat;

			$degrees = $matches['longitude_degrees'];
			$minutes = $matches['longitude_minutes'];
			$seconds = $matches['longtitude_seconds'];
			$hemisphere = $matches['longitude_hemisphere'];
			$long = $degrees + ($minutes/60.0) + ($seconds/3600);
			if ($hemisphere == 'W') { $long *= -1.0; };
			
			$locality->decimalLongitude = $long;
			
		}
		
		
	}
	
	if ($debug)
	{
		echo "Trying line " . __LINE__ . "\n";
	}
	/*
	// Some records have lat and lon in isolation_source, e.g. AY922971
	if (isset($locality->isolation_source))
	{
		$isolation_source = $locality->isolation_source;
		$matches = array();
		if (preg_match('/([0-9]+\.[0-9]+) (N|S), ([0-9]+\.[0-9]+) (W|E)/i', $isolation_source, $matches))
		{
			if ($debug) { print_r($matches); }	
			
			$locality->{'http://rs.tdwg.org/dwc/terms/decimalLatitude'}[0] = $matches[1];
			if ($matches[2] == 'S')
			{
				$seq['source']['latitude'] *= -1;
			}
			$locality->{'http://rs.tdwg.org/dwc/terms/decimalLongitude'}[0] = $matches[3];
			if ($matches[4] == 'W')
			{
				$seq['source']['longitude'] *= -1;
			}
			if  (!isset($locality->decimalLocality))
			{
				$locality->decimalLocality = $locality->isolation_source;
			}
		}
	}
	*/
	
}	

//----------------------------------------------------------------------------------------
function genbank_xml_to_json($xml)
{		
	$data = null;
		
	// delete some things which may cause problems for JSON
	$xml = str_replace('<GBFeature_partial5 value="true"/>', '', $xml);
	$xml = str_replace('<GBFeature_partial3 value="true"/>', '', $xml);
	$xml = str_replace('<GBQualifier_value></GBQualifier_value>', '', $xml);

	if ($xml != '')
	{
		$xp = new XsltProcessor();
		$xsl = new DomDocument;
		$xsl->load(dirname(__FILE__) . '/xml2json.xslt');
		$xp->importStylesheet($xsl);
		
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		$xpath = new DOMXPath($dom);
	
		$json = $xp->transformToXML($dom);
	
		// fix "-" in variable names
		// fix "-" in variable names
		$json = str_replace('"GBSeq_feature-table"', 		'"GBSeq_feature_table"', $json);
		$json = str_replace('"GBSeq_primary-accession"', 	'"GBSeq_primary_accession"', $json);
		$json = str_replace('"GBSeq_other-seqids"', 		'"GBSeq_other_seqids"', $json);

		$json = str_replace('"GBSeq_update-date"', 			'"GBSeq_update_date"', $json);
		$json = str_replace('"GBSeq_create-date"', 			'"GBSeq_create_date"', $json);
		$json = str_replace('"GBSeq_accession-version"', 	'"GBSeq_accession_version"', $json);
		
		// idiosyncratic fixes
		// JF279882
		$json = str_replace('"GBQualifier_value":45307E', 	'"GBQualifier_value":"45307E"', $json);
		
		
		//echo $json;
		
		$sequences = json_decode($json);
				
		if (!isset($sequences->GBSet))
		{
			echo "Not found\n";
			exit();
		}
	
		foreach ($sequences->GBSet as $GBSet)
		{		
			$obj = new stdclass;
		
			$obj->sequence = new stdclass;
			
			$obj->sequence->accession = $GBSet->GBSeq_primary_accession;
			$obj->sequence->accession_version = $GBSet->GBSeq_accession_version;
			//$obj->version = str_replace($obj->sequence->accession, '', $obj->sequence->accession_version);
			//$obj->version = str_replace('.', '', $obj->version);
			
			$obj->identification = new stdclass;
			$obj->sample = new stdclass;
			
			$obj->links = array();	
			
			
			// dates
			if (false != strtotime($GBSet->GBSeq_update_date))
			{
				$obj->update_date = parse_ncbi_date($GBSet->GBSeq_update_date);
			}	
			if (false != strtotime($GBSet->GBSeq_create_date))
			{
				$obj->create_date = parse_ncbi_date($GBSet->GBSeq_create_date);
			}
						
			// other ids
			foreach ($GBSet->GBSeq_other_seqids as $seqids)
			{
				if (preg_match('/gi\|(?<gi>\d+)$/', $seqids, $m))
				{
					$obj->sequence->gi = $m['gi'];
				}
			}			
			
			// keywords
			if (isset($GBSet->GBSeq_keywords))
			{
				$obj->keywords = array();
				foreach ($GBSet->GBSeq_keywords as $k => $v)
				{
					$obj->keywords[] = $v;
					
					if ($v == 'BARCODE')
					{
						$obj->sequence->barcode = true;
					}
				}
			}
			
			// projects
			if (isset($GBSet->GBSeq_project))
			{
				$obj->project = $GBSet->GBSeq_project;
			}
			
			// taxonomy
			if (isset($GBSet->GBSeq_taxonomy))
			{
				 $obj->identification->taxonomy = preg_split('/;\s*/', $GBSet->GBSeq_taxonomy);
			}
			
			
			if (isset($GBSet->GBSeq_definition))
			{
				 $obj->definition = $GBSet->GBSeq_definition;
			}
			
			// get links
			
			// sequence links to NCBI taxon, publications, and specimens
			
			// source
			foreach ($GBSet->GBSeq_feature_table as $feature_table)
			{
				switch ($feature_table->GBFeature_key)
				{
					case 'source':
						foreach ($feature_table->GBFeature_quals as $feature_quals)
						{
							switch ($feature_quals->GBQualifier_name)
							{
								// Database cross links
								case 'db_xref':
									// NCBI taxonomy
									if (preg_match('/taxon:(?<id>\d+)$/', $feature_quals->GBQualifier_value, $m))
									{
										$obj->identification->taxonID = $m['id'];
										$obj->links[] = 'http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=' . $m['id'] ;			
									}
									
									// DNA barcode
									if (preg_match('/BOLD:(?<id>.*)$/', $feature_quals->GBQualifier_value, $m))
									{
										$bold = $m['id'];
										$bold = str_replace('.COI-5P', '', $bold);
										
										$obj->links[] = 'http://bins.boldsystems.org/index.php/Public_RecordView?processid=' . $bold;			
										
										// Store BOLD to help matching
										$obj->sample->otherCatalogNumbers[] = $bold;
									}
									break;
									
								// Locality-----------------------------------------------
								case 'country':
								case 'locality':
									if (!isset($obj->locality))
									{
										$obj->locality = new stdclass;
									}
									$obj->locality->{$feature_quals->GBQualifier_name} = $feature_quals->GBQualifier_value;
									break;
									
								// latitude and longitude (needs parsing)
								case 'lat_lon':
									if (!isset($obj->locality))
									{
										$obj->locality = new stdclass;
									}
									process_lat_lon($obj->locality, $feature_quals->GBQualifier_value);
									break;
									
								// Identification-----------------------------------------
								case 'identified_by':
									$obj->identification->identifiedBy = $feature_quals->GBQualifier_value;
									break;									

								case 'organism':
									$obj->identification->organism = $feature_quals->GBQualifier_value;
									break;

									
								// Event -------------------------------------------------
								case 'collection_date':
									if (!isset($obj->event))
									{
										$obj->event = new stdclass;
									}
									$obj->event->verbatimEventDate = $feature_quals->GBQualifier_value;
									
									// day month year
									if (preg_match('/^[0-9]{2}-[A-Z][a-z]{2}-[0-9]{4}$/', $feature_quals->GBQualifier_value))
									{
										if (false != strtotime($feature_quals->GBQualifier_value))
										{
											$obj->event->eventDate = parse_ncbi_date($feature_quals->GBQualifier_value);
										}
									}
									
									// month year
									if (preg_match('/^[A-Z][a-z]{2}-[0-9]{4}$/', $feature_quals->GBQualifier_value))
									{
										if (false != strtotime($feature_quals->GBQualifier_value))
										{
											$obj->event->eventDate = explode("-", date("Y-n", strtotime($feature_quals->GBQualifier_value)));
										}
									}

									// Year only
									if (preg_match('/^[0-9]{4}$/', $feature_quals->GBQualifier_value))
									{
										$obj->event->eventDate[] = $feature_quals->GBQualifier_value;
									}
									break;
									
									
								// Sample-------------------------------------------------
								case 'collected_by':
									if (!isset($obj->event))
									{
										$obj->event = new stdclass;
									}
									$obj->event->collected_by = $feature_quals->GBQualifier_value;
									break;
									
								// Sample-------------------------------------------------
								case 'clone':
									$obj->sample->clone = $feature_quals->GBQualifier_value;
									break;

								case 'host':
									$obj->sample->host = $feature_quals->GBQualifier_value;
									break;
									
								case 'isolate':
									$obj->sample->recordNumber = $feature_quals->GBQualifier_value;
									break;

								case 'isolation_source':
									$obj->sample->isolation_source = $feature_quals->GBQualifier_value;
									break;
									
								case 'environmental_sample':
									$obj->sample->environmental_sample = true;
									break;

								case 'note':
									$obj->sample->note = $feature_quals->GBQualifier_value;
									break;
									
								case 'organelle':
									$obj->sample->organelle = $feature_quals->GBQualifier_value;
									break;
									
								case 'specimen_voucher':
									//echo $feature_quals->GBQualifier_value . "\n";
									$obj->sample->otherCatalogNumbers[] = $feature_quals->GBQualifier_value;
									
									// Try to interpret them
									$matched = false;
									
									// TM<ZAF>40766
									if (!$matched)
									{
										if (preg_match('/^(?<institutionCode>(?<prefix>[A-Z]+)\<[A-Z]+\>)(?<catalogNumber>\d+)$/', $feature_quals->GBQualifier_value, $m))
										{
											$obj->sample->institutionCode 	=  $m['institutionCode'];
											$obj->sample->catalogNumber 	=  $m['catalogNumber'];
											$matched = true;
										}
									}
																		
									if (!$matched)
									{
										if (preg_match('/^(?<institutionCode>[A-Z]+):(?<collectionCode>.*):(?<catalogNumber>\d+)$/', $feature_quals->GBQualifier_value, $m))
										{
											$obj->sample->institutionCode 	= $m['institutionCode'];
											$obj->sample->collectionCode 	= $m['collectionCode'];
											$obj->sample->catalogNumber 	= $m['catalogNumber'];
											
											$obj->sample->otherCatalogNumbers[] = $obj->sample->institutionCode . ' ' . $obj->sample->catalogNumber ;
											
											$matched = true;
										}
									}
									
									if (!$matched)
									{
										if (preg_match('/^(?<institutionCode>[A-Z]+)[\s|:]?(?<catalogNumber>\d+)$/', $feature_quals->GBQualifier_value, $m))
										{
											$obj->sample->institutionCode 	=  $m['institutionCode'];
											$obj->sample->catalogNumber 	=  $m['catalogNumber'];
											
											// post process to help matching
											switch ($m['institutionCode'])
											{
												case 'KUNHM':
													$obj->sample->otherCatalogNumbers[] = 'KU' .  ' ' . $m['catalogNumber'];
													break;
													
												default:
													break;
											}
											$matched = true;
										}
									}
									break;	
									
								default:
									break;
							}
						}
						break;
						
					// Sequence-----------------------------------------------
					case 'CDS':
					case 'rRNA':
						foreach ($feature_table->GBFeature_quals as $feature_quals)
						{
							switch ($feature_quals->GBQualifier_name)
							{
								case 'gene':
									if (!isset($obj->sequence->gene))
									{
										$obj->sequence->gene = array();
									}
									$obj->sequence->gene[] = $feature_quals->GBQualifier_value;
									break;

								case 'product':
									if (!isset($obj->sequence->product))
									{
										$obj->sequence->product = array();
									}
									$obj->sequence->product[] = $feature_quals->GBQualifier_value;
									break;

								default:
									break;
							}					
						}						
						break;
						
						
					default:
						break;
				}
			}
			
			// process locality
			if (isset($obj->locality))
			{
				process_locality($obj->locality);
				/*
				if (isset($obj->locality->decimalLatitude) && isset($obj->locality->decimalLongitude))
				{
					$geohash = new GeoHash();
					$geohash->SetLatitude($obj->locality->decimalLatitude);
					$geohash->SetLongitude($obj->locality->decimalLongitude);
		
					$obj->locality->geohash = $geohash->getHash();		
				}
				*/
			}
			
			
			// Specimen links?
			//nucleotide_links($obj->sequence->gi)
					
			
			// references
			$obj->references = array();
			foreach ($GBSet->GBSeq_references as $GBReference)
			{
				$reference = new stdclass;
				
				$skip = false;  // use flag to skip some references (e.g., direct submission)
				
				// Do we have an external identifier?								
				if (isset($GBReference->GBReference_pubmed))
				{
					$reference->pmid = $GBReference->GBReference_pubmed;
				}
				
				if (isset($GBReference->GBReference_xref))
				{
					if ($GBReference->GBReference_xref->GBXref->GBXref_dbname == 'doi')
					{
						$reference->doi = $GBReference->GBReference_xref->GBXref->GBXref_id;
					}
				}			
				
				if (isset($reference->pmid) || isset($reference->doi))
				{
				}
				else
				{
					// Reference without identifier
					
					
					// title
					$reference->title = $GBReference->GBReference_title;
					
					// bibliographic citation
					if (isset($GBReference->GBReference_journal))
					{
						$reference->bibliographicCitation = $GBReference->GBReference_journal;
					
						if ($GBReference->GBReference_title == 'Direct Submission')
						{
							$skip = true;
						}
						
						if ($reference->bibliographicCitation == "Unpublished")
						{
							$skip = true;
						}
						
						if (!$skip)
						{		
							// Look for identifier
							$citation = $GBReference->GBReference_title . '. ' . $GBReference->GBReference_journal;
							
				
							$result = crossref_search($citation);
							if (isset($result->doi))
							{
								$reference->doi = $result->doi;
							}
						
											
							// Parse citation string into component parts							
							if (preg_match('/(?<journal>.*)\s+(?<volume>\d+)(\s+\((?<issue>.*)\))?,\s+(?<spage>\d+)-(?<epage>\d+)\s+\((?<year>[0-9]{4})\)/', $GBReference->GBReference_journal, $m))
							{
								$reference->journal = $m['journal'];							
								$reference->volume = $m['volume'];								
								if ($m['issue'] != '')
								{
									$reference->issue = $m['issue'];
								}
								$reference->pageStart = $m['spage'];
								if ($m['epage'] != '')
								{
									$reference->pageEnd = $m['epage'];
								}
								$reference->year = $m['year'];
							}
							
							if (isset($GBReference->GBReference_authors))
							{
								foreach ($GBReference->GBReference_authors as $a)
								{
									$parts = parse_name($a);					
									$author = new stdClass();
									if (isset($parts['last']))
									{
										$author->family = $parts['last'];
									}
									if (isset($parts['first']))
									{
										$author->given = $parts['first'];
						
										if (array_key_exists('middle', $parts))
										{
											$author->given .= ' ' . $parts['middle'];
										}
										$author->given = preg_replace('/\.([A-Z])/', '. $1', $author->given);						
									}

									$reference->author[] = $author;					
								}
							}		
							
							
							// How do we handle "unpublished"?
							
							
							// Can we augment with a DOI?
							$result = crossref_search($reference->title . ' ' . $reference->bibliographicCitation);
							if ($result)
							{
								if ($result->match)
								{
									$reference->doi = $result->doi;		
								}
							}
							
							
							
						}
					}
				}
				
				if (!$skip)
				{
					if (isset($reference->pmid) || isset($reference->doi))
					{
						// We have an external identifier we can use				
						if (isset($reference->pmid))
						{
							$obj->links[] = 'http://www.ncbi.nlm.nih.gov/pubmed/' . $reference->pmid;
						}
						else
						{
							if (isset($reference->doi))
							{
								$obj->links[] = 'http://dx.doi.org/' . $reference->doi;
							}
						}
					}
				
					$obj->references[] = $reference;
				}
			
			}

	
		}
		
		$data = new stdclass;
		$data->{'message-format'} ='genbank';
		$data->message = $obj;
		
	}
	
	
	
	return $data;

}


//----------------------------------------------------------------------------------------
function genbank_fetch($id)
{	
	$data = null;
	
	// API call
	$parameters = array(
		'db' 		=> 'nucleotide',
		'id'		=> $id,
		'rettype'	=> 'gb',
		'retmode'	=> 'xml',
		
		// skip sequences so that we don't barf over genomes
		'seq_start'	=> 1,
		'seq_stop'	=> 1
	);
	
	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?' . http_build_query($parameters);
	
	//echo $url;
	
	$xml = get($url);
	
	//echo "$xml\n";
	
	if ($xml != '')
	{
		$data = genbank_xml_to_json($xml);
	}

	return $data;

}
	
//----------------------------------------------------------------------------------------

if (0)
{
	//genbank_fetch('DQ381473');
	
	$accession = 'AP008239'; // unpublished
	
	$accession = 'KC860804';
	//$accession = 'GU224788';
	//$accession = 'DQ650615';
	//$accession = 'HQ733947';
	//$accession = 'KF185038';
	
	//$accession = 'FR686779'; // georeferenced, citation needs DOI
	//$accession = 'AM779676';
	
	//$accession = 'HM067338'; //Limnonectes cf. kuhlii 'lineage 9', CAS 235132, georeferenced in GBIF
	
	//$accession = 'EU139271.1';
	
	//$accession = 'AB332438'; // plankton sample
	
	//$accession = 'KP420745'; //Little Barrier next gen
	//$accession = 'AB333773';
	
	//$accession = 'GQ260888'; // barcode crustacean
	
	//$accession = 'AJ556909'; // in press, actually published in BioStor http://biostor.org/reference/144878
	
	//$accession = 'JN106060'; // holotype specimen, see http://iphylo.blogspot.co.uk/2014/01/ncbi-taxonomy-database-now-shows-type.html
	
	//$accession = 'FJ769122';
	
	//$accession = 'HQ600867';
	
	//$accession = 'JN107891'; // host
	//$accession = 'JF434865';
	
	$accession = 'KF268949'; // Zika virus with mosquito host Aedes opok
	
	//$accession = 'EF543742';
	
	//$accession = 'HM067338'; //Limnonectes cf. kuhlii 'lineage 9', CAS 235132, georeferenced in GBIF
	
	//$accession = 'HQ600867';
	
//	$accession = 'EU139271'; // barcode

	$accession = 'EF543742'; // same as http://www.gbif.org/occurrence/999838402
	//$accession = 'GQ260888'; // barcode crustacean
	
	$data = genbank_fetch($accession);
	print_r($data);
	
	echo json_encode($data);
	
}

if (0)
{
	// JN270496
	$xml = file_get_contents('JQ173912.xml');
	//$xml = file_get_contents('JN270496.xml');
	
	//echo $xml;
	$data = genbank_xml_to_json($xml);
	print_r($data);
	
}

?>
