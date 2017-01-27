<?php

//--------------------------------------------------------------------------------------------------
/**
 * @brief Format JSON nicely
 *
 * From umbrae at gmail dot com posted 10-Jan-2008 06:21 to http://uk3.php.net/json_encode
 *
 * @param json Original JSON
 *
 * @result Formatted JSON
 */
function json_format($json)
{
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

/*    $json_obj = json_decode($json);

    if($json_obj === false)
        return false;

    $json = json_encode($json_obj); */
    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
        $char = $json[$c];
        switch($char)
        {
            case '{':
            case '[':
                if(!$in_string)
                {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '}':
            case ']':
                if(!$in_string)
                {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ',':
                if(!$in_string)
                {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ':':
                if(!$in_string)
                {
                    $new_json .= ": ";
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '"':
                if($c > 0 && $json[$c-1] != '\\')
                {
                    $in_string = !$in_string;
                }
            default:
                $new_json .= $char;
                break;                    
        }
    }

    return $new_json;
}



$filename = 'phytokeys-1426.xml';
$filename = 'elife00778.xml';
//$filename = 'Nota Lepidopterologica.xml';

//$filename = 'ZooKeys-169-001.xml';

//$filename = '561352.xml'; // no

if (1)
{
	$basedir = dirname(__FILE__);
	$filename = $basedir . '/' . 'pone.0024047.nxml'; // problem with citation clicking
}

if (1)
{
	$basedir = dirname(__FILE__);
	$filename = $basedir . '/' . 'Nota Lepidopterologica.xml'; 
}

if (1)
{
	$basedir = dirname(__FILE__);
	$filename = $basedir . '/' . 'phytokeys-1426.xml'; 
}


//$filename = '1756-3305-6-221.nxml';

/*
$filename = 'J_Insect_Sci_2015_Apr_15_15(1)_47/iev028.nxml';
*/

if (1)
{
	$basedir = 'Zookeys_2014_Mar_25_(393)_1-107';
	$filename = $basedir . '/' . 'zookeys-393-001.nxml';
}


if (0)
{
	$basedir = 'J_Insect_Sci_2015_Apr_15_15(1)_47';
	$filename = $basedir . '/' . 'iev028.nxml';
}



$xml = file_get_contents($filename);

$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);



$heading_count = 0;
$text_count = 0;
$paragraph_count = 0;
$figure_count = 0;
$caption_count = 0;

$html_table_count = 0;

$emphasis_count = 0;
$strong_count = 0;

$citation_reference_count = 0;
$figure_reference_count = 0;



$document = new stdclass;
$document->id = 1;
$document->schema = array("lens-article", "2.0.0");
$document->nodes = new stdclass;

$document->nodes->document = new stdclass;
$document->nodes->document->id = "document";
$document->nodes->document->type = "document";
$document->nodes->document->guid = 1;
$document->nodes->document->title = "title"; 
$document->nodes->document->abstract = "";
$document->nodes->document->authors = array();
$document->nodes->document->views 
	= array(
        "content",
        "figures",
        "citations",
        "definitions",
        "info");

$document->nodes->content = new stdclass;
$document->nodes->content->id = "content";
$document->nodes->content->type = "view";
$document->nodes->content->nodes = array();

$document->nodes->figures = new stdclass;
$document->nodes->figures->id = "figures";
$document->nodes->figures->type = "view";
$document->nodes->figures->nodes = array();

$document->nodes->citations = new stdclass;
$document->nodes->citations->id = "citations";
$document->nodes->citations->type = "view";
$document->nodes->citations->nodes = array();

$document->nodes->definitions = new stdclass;
$document->nodes->definitions->id = "definitions";
$document->nodes->definitions->type = "view";
$document->nodes->definitions->nodes = array();

$document->nodes->info = new stdclass;
$document->nodes->info->id = "info";
$document->nodes->info->type = "view";
$document->nodes->info->nodes = array();


//----------------------------------------------------------------------------------------
// info

$nc = $xpath->query ('//article-meta/title-group/article-title', $node);
foreach ($nc as $n)
{
	$document->nodes->document->title = $n->nodeValue;
}


//----------------------------------------------------------------------------------------
// articleinfo
$document->nodes->articleinfo = new stdclass;
$document->nodes->articleinfo->id = "articleinfo";
$document->nodes->articleinfo->type = "paragraph";
$document->nodes->articleinfo->children = array();

//----------------------------------------------------------------------------------------
// publication info
$document->nodes->publication_info = new stdclass;
$document->nodes->publication_info->id = "publication_info";
$document->nodes->publication_info->type = "publication_info";

$document->nodes->publication_info->journal = 'x';
$document->nodes->publication_info->doi = '10.x';
$document->nodes->publication_info->article_info = 'articleinfo';
$document->nodes->publication_info->article_type = 'Research article';
$document->nodes->publication_info->links = array();

// DOI
$nc = $xpath->query ('//article-meta/article-id[@pub-id-type="doi"]', $node);
foreach ($nc as $n)
{
	$document->nodes->publication_info->doi = $n->firstChild->nodeValue;
}


$document->nodes->info->nodes[] = $document->nodes->publication_info->id;


//----------------------------------------------------------------------------------------
// cover

$document->nodes->cover = new stdclass;
$document->nodes->cover->id = "cover";
$document->nodes->cover->type = "cover";
$document->nodes->cover->title = "";
$document->nodes->cover->authors = array();
$document->nodes->cover->abstract = '';

$document->nodes->content->nodes[] = $document->nodes->cover->id;


//----------------------------------------------------------------------------------------
// acknowledgments
$nodeCollection = $xpath->query ('//back/ack');
foreach ($nodeCollection as $node)
{
	$heading_count++;
	$heading = new stdclass;
	$heading->id = "heading_" . $heading_count;
	$heading->type = "heading";
	$heading->level = 3;
	$heading->content = "Acknowledgements";
	
	$text_count++;
	$text = new stdclass;
	$text->id = "text_" . $text_count;
	$text->type = "text";
	$text->content = '';

	$paragraph_count++;
	$paragraph = new stdclass;
	$paragraph->id = "paragraph_" . $paragraph_count;
	$paragraph->type = "paragraph";
	$paragraph->children = array();
	$paragraph->children[] = $text->id;
	$paragraph->source_id = null;
	
	$nc = $xpath->query ('p', $node);
	foreach ($nc as $n)
	{
		$text->content .= $n->nodeValue;
	}
		
	$document->nodes->{$heading->id} 	= $heading;
	$document->nodes->{$text->id} 		= $text;
	$document->nodes->{$paragraph->id}	= $paragraph;
	
	$document->nodes->info->nodes[] 	= $heading->id;
	$document->nodes->info->nodes[] 	= $paragraph->id;

}


//----------------------------------------------------------------------------------------
// authors

$author_count = 0;

$nodeCollection = $xpath->query ('//contrib-group/contrib[@contrib-type="author"]');
foreach ($nodeCollection as $node)
{
	$citation_count++;
	
	$contributor = new stdclass;
	$contributor->id = 'contributor_' . $citation_count;
	$contributor->type = "contributor";
	
	$contributor->name = "";
	
	$contributor->competing_interests = array(); 
	$contributor->emails = array(); 
	$contributor->fundings = array(); 
	$contributor->members = array(); 
	
	$text_contributor_reference = new stdclass;
	$text_contributor_reference->id = 'text_contributor_' . $citation_count . '_reference';
	$text_contributor_reference->type = "text";
	
	$document->nodes->cover->authors[] = $text_contributor_reference->id;
	
	$contributor_reference = new stdclass;
	$contributor_reference->id = 'contributor_reference_' . $citation_count;
	$contributor_reference->type = "contributor_reference";
	$contributor_reference->path = array(
		$text_contributor_reference->id,
		"content"
	);
	$contributor_reference->target = $contributor->id;
	
 
	// person-group
	$nc2 = $xpath->query ('name', $node);
	foreach ($nc2 as $n2)
	{
		$name = array();
		$nc3 = $xpath->query ('given-names', $n2);
		foreach ($nc3 as $n3)
		{
			$name[] = trim($n3->nodeValue);
		}
		$nc3 = $xpath->query ('surname', $n2);
		foreach ($nc3 as $n3)
		{
			$name[] = trim($n3->nodeValue);
		}
		$contributor->name = join(' ', $name);
		
		$text_contributor_reference->content = $contributor->name;
		
		$contributor_reference->range = array(0, strlen($contributor->name));
	}
	
	
	$contributor->affiliations = array();

	$document->nodes->document->authors[] = $contributor->id;
	$document->nodes->{$contributor->id} = $contributor;
	
	$document->nodes->{$text_contributor_reference->id} = $text_contributor_reference;
	$document->nodes->{$contributor_reference->id} = $contributor_reference;
	
	$document->nodes->info->nodes[] = $contributor->id;
	

}




//----------------------------------------------------------------------------------------
// abstract
$nodeCollection = $xpath->query ('//article-meta/abstract[1]');
foreach ($nodeCollection as $node)
{
	$heading_count++;
	$heading = new stdclass;
	$heading->id = "heading_" . $heading_count;
	$heading->type = "heading";
	$heading->level = 1;
	$heading->content = "Abstract";
	
	$text_count++;
	$text = new stdclass;
	$text->id = "text_" . $text_count;
	$text->type = "text";
	$text->content = '';

	$paragraph_count++;
	$paragraph = new stdclass;
	$paragraph->id = "paragraph_" . $paragraph_count;
	$paragraph->type = "paragraph";
	$paragraph->children = array();
	$paragraph->children[] = $text->id;
	$paragraph->source_id = null;
	
	$nc = $xpath->query ('p', $node);
	foreach ($nc as $n)
	{
		$text->content .= $n->nodeValue;
	}
		
	$document->nodes->{$heading->id} 	= $heading;
	$document->nodes->{$text->id} 		= $text;
	$document->nodes->{$paragraph->id}	= $paragraph;
	
	$document->nodes->content->nodes[] 	= $heading->id;
	$document->nodes->content->nodes[] 	= $paragraph->id;

}

//----------------------------------------------------------------------------------------
// text
$nodeCollection = $xpath->query ('//body/sec');
foreach ($nodeCollection as $node)
{
	// title
	$nc = $xpath->query ('title', $node);
	foreach ($nc as $n)
	{
		$heading_count++;
		$heading = new stdclass;
		$heading->id = "heading_" . $heading_count;
		$heading->type = "heading";
		$heading->level = 2;
		$heading->content = $n->nodeValue;
		
		$nc2 = $xpath->query ('@id', $n);
		foreach ($nc2 as $n2)
		{
			$heading->source_id = $n2->firstChild->nodeValue;
		}		
		
		$document->nodes->{$heading->id} 	= $heading;
		$document->nodes->content->nodes[] 	= $heading->id;
	}
	
	// text
	if (0)
	{
		$nc = $xpath->query ('p', $node);
		foreach ($nc as $n)
		{
			$text_count++;
			$text = new stdclass;
			$text->id = "text_" . $text_count;
			$text->type = "text";
			$text->content = '';

			$paragraph_count++;
			$paragraph = new stdclass;
			$paragraph->id = "paragraph_" . $paragraph_count;
			$paragraph->type = "paragraph";
			$paragraph->children = array();
			$paragraph->children[] = $text->id;
			$paragraph->source_id = null;
	
			$text->content = $n->nodeValue;
		
			$document->nodes->{$text->id} 		= $text;
			$document->nodes->{$paragraph->id}	= $paragraph;
	
			$document->nodes->content->nodes[] 	= $paragraph->id;
		}
	}
	else
	{
		// Process paragraph nodes
		$nc = $xpath->query ('p', $node);
		
		$text = null;
		$paragraph = null;
				
		foreach ($nc as $n)
		{
			$paragraph_count++;
			$paragraph = new stdclass;
			$paragraph->id = "paragraph_" . $paragraph_count;
			$paragraph->type = "paragraph";
			$paragraph->children = array();
			$paragraph->source_id = null;
			
			$text = null;
			
			$pos = 0;
		
			foreach ($n->childNodes as $children) 
			{			
				if (!$text)
				{
					$text_count++;
					$text = new stdclass;
					$text->id = "text_" . $text_count;
					$text->type = "text";
					//$text->content = '[' . $children->nodeName . ']' . $children->nodeValue;
					$text->content = $children->nodeValue;
					
					
					
					$paragraph->children[] = $text->id;
				}
				else
				{
					//$text->content .= '[' . $children->nodeName . ']' . $children->nodeValue;
					$text->content .=  $children->nodeValue;
					
					
				}
				
				//echo __LINE__ . " " . $children->nodeName . "\n";
				
				switch ($children->nodeName)
				{
					// eat tables 
					case 'table-wrap':
						break;
				
					case 'xref':					
						if ($children->hasAttributes()) 
						{ 
							$attributes = $children->attributes; 
							
							//print_r($attributes);
							
							$xref = array();
							
							foreach ($attributes as $attribute)
							{
								switch ($attribute->name)
								{
									case 'ref-type':
										$xref['ref-type'] = $attribute->value;
										break;
									case 'rid':
										$xref['rid'] = $attribute->value;
										break;
							
									default:
										break;
								}
							}
							
							switch ($xref['ref-type'])
							{
								case 'bibr':
									$citation_reference_count++;
						
									$citation_reference = new stdclass;
									$citation_reference->id = 'citation_reference_' . $citation_reference_count;
									$citation_reference->type = "citation_reference";	
								
														
									$citation_reference->target = 'article_citation_' . str_replace('bib', '', $xref['rid']);
								
									$citation_reference->path = array();
									$citation_reference->path[] = $text->id;
									$citation_reference->path[] = "content";
								
									$citation_reference->range = array();
									$citation_reference->range[] = $pos;
									$citation_reference->range[] = $pos + mb_strlen($children->nodeValue, mb_detect_encoding($children->nodeValue));
								
									$document->nodes->{$citation_reference->id} 		= $citation_reference;
									break;

								// figures and tables
								case 'fig':
								case 'table':
									$figure_reference_count++;
						
									$figure_reference = new stdclass;
									$figure_reference->id = 'figure_reference_' . $figure_reference_count;
									$figure_reference->type = "figure_reference";	
								
									$id = $xref['rid'];
									$id = preg_replace('/.*[T|F](\d+)$/', '$1', $id);
									if ($xref['ref-type'] == 'fig')
									{
										$figure_reference->target = 'figure_' . $id;
									}
									else
									{
										$figure_reference->target = 'html_table_' . $id;									
									}
								
									$figure_reference->path = array();
									$figure_reference->path[] = $text->id;
									$figure_reference->path[] = "content";
								
									$figure_reference->range = array();
									$figure_reference->range[] = $pos;
									$figure_reference->range[] = $pos + mb_strlen($children->nodeValue, mb_detect_encoding($children->nodeValue));
								
									$document->nodes->{$figure_reference->id} 		= $figure_reference;
									break;

									
								default:
									break;
							}
						}			
						break;
						
					case 'bold':
						$strong_count++;
				
						$strong = new stdclass;
						$strong->id = 'strong' . $strong_count;
						$strong->type = "strong";	
						
						$strong->path = array();
						$strong->path[] = $text->id;
						$strong->path[] = "content";
						
						$strong->range = array();
						$strong->range[] = $pos;
						$strong->range[] = $pos + mb_strlen($children->nodeValue, mb_detect_encoding($children->nodeValue));
						
						$document->nodes->{$strong->id} 		= $strong;					
						break;
						
						
					case 'italic':
						$emphasis_count++;
				
						$emphasis = new stdclass;
						$emphasis->id = 'emphasis_' . $emphasis_count;
						$emphasis->type = "emphasis";	
						
						$emphasis->path = array();
						$emphasis->path[] = $text->id;
						$emphasis->path[] = "content";
						
						$emphasis->range = array();
						$emphasis->range[] = $pos;
						$emphasis->range[] = $pos + mb_strlen($children->nodeValue, mb_detect_encoding($children->nodeValue));
						
						$document->nodes->{$emphasis->id} 		= $emphasis;					
						break;
						
					default:
						break;
				}
				
				
				$pos += mb_strlen($children->nodeValue, mb_detect_encoding($children->nodeValue));
				
				
				
			}
			
			$document->nodes->{$text->id} 		= $text;
			$document->nodes->{$paragraph->id}	= $paragraph;
			$document->nodes->content->nodes[] 	= $paragraph->id;
					
		}
				
	}
	
}




//----------------------------------------------------------------------------------------
if (1)
{
// figures
/*
    "figure_1": {
      "type": "figure",
      "id": "figure_1",
      "source_id": "fig1",
      "label": "Figure 1.",
      "url": "http://cdn.elifesciences.org/elife-articles/00778/jpg/elife00778f001.jpg",
      "caption": "caption_1",
      "position": "float"
    },
    
<fig id="F1" position="float" orientation="portrait">
                    <label>Figures 1–4.</label>
                    <caption><p>Wing pattern of adults (males). 1-2: <italic><tp:taxon-name><tp:taxon-name-part taxon-name-part-type="genus">Scotopteryx</tp:taxon-name-part> <tp:taxon-name-part taxon-name-part-type="species">kurmanjiana</tp:taxon-name-part></tp:taxon-name></italic> sp. n. 1. Holotype; NE Quchan, Iran; 2. Paratype; Garrygala, S Turkmenistan; 3-4: <italic><tp:taxon-name><tp:taxon-name-part taxon-name-part-type="genus">Scotopteryx</tp:taxon-name-part> <tp:taxon-name-part taxon-name-part-type="species">kuznetzovi</tp:taxon-name-part></tp:taxon-name></italic>. 3. Guzeldere Pass, E. Turkey; 4. Basmendj, NW Iran; a: dorsal view, b: ventral view.</p></caption>
                    <graphic xlink:href="nota_lepi_-037-037-g001.jpg" position="float" orientation="portrait" xlink:type="simple" id="oo_8153.jpg"/>
                </fig>
                    
*/
$nodeCollection = $xpath->query ('//fig');
foreach ($nodeCollection as $node)
{
	$figure_count++;
	$figure = new stdclass;
	$figure->id = "figure_" . $figure_count;
	$figure->type = "figure";
	$figure->source_id = "";
	$figure->label = "";
	$figure->url = "";
	$figure->caption = null;
	$figure->position = "";
	
	$nc = $xpath->query ('@id', $node);
	foreach ($nc as $n)
	{
		$figure->source_id = $n->firstChild->nodeValue;
	}

	$nc = $xpath->query ('@position', $node);
	foreach ($nc as $n)
	{
		$figure->position = $n->firstChild->nodeValue;
	}

	$nc = $xpath->query ('label', $node);
	foreach ($nc as $n)
	{
		$figure->label = $n->firstChild->nodeValue;
	}
	
	$nc = $xpath->query ('graphic/@xlink:href', $node);
	foreach ($nc as $n)
	{
		//$figure->url = $n->firstChild->nodeValue;

		$figure->url = '../../sources/lens/' . $basedir . '/' . $n->firstChild->nodeValue . '.jpg';
		
	}
	
	$nc = $xpath->query ('caption', $node);
	foreach ($nc as $n)
	{
		
		$caption_count++;
		
		$caption = new stdclass;
		$caption->id = 'caption_' . $caption_count;
		$caption->type = "caption";
		$caption->source_id = null;
		//$caption->title = null;
		$caption->children = array();
		
		// to do
		$nc2 = $xpath->query ('p', $n);
		foreach ($nc2 as $n2)
		{
		
			$text_count++;
			$text = new stdclass;
			$text->id = "text_" . $text_count;
			$text->type = "text";
			$text->content = '';

			$paragraph_count++;
			$paragraph = new stdclass;
			$paragraph->id = "paragraph_" . $paragraph_count;
			$paragraph->type = "paragraph";
			$paragraph->children = array();
			$paragraph->children[] = $text->id;
			$paragraph->source_id = null;
	
			$text->content = $n2->nodeValue;
		
			$document->nodes->{$text->id} 		= $text;
			$document->nodes->{$paragraph->id}	= $paragraph;
	
			$caption->children[] 	= $paragraph->id;
		}
		$figure->caption = $caption->id;
		
		$document->nodes->{$caption->id} 	= $caption;
	}
	

	
	$document->nodes->{$figure->id} 	= $figure;
	$document->nodes->figures->nodes[] 	= $figure->id;

}
}

//----------------------------------------------------------------------------------------
if (1)
{
// tables
/*
    "figure_1": {
      "type": "figure",
      "id": "figure_1",
      "source_id": "fig1",
      "label": "Figure 1.",
      "url": "http://cdn.elifesciences.org/elife-articles/00778/jpg/elife00778f001.jpg",
      "caption": "caption_1",
      "position": "float"
    },
    
<table-wrap id="iev028-T1" position="float"><label>Table 1.</label>
<caption><p>Details of the study localities</p></caption>
<table frame="hsides" rules="groups">...                    
*/
$nodeCollection = $xpath->query ('//table-wrap');
foreach ($nodeCollection as $node)
{
	$html_table_count++;
	$html_table = new stdclass;
	$html_table->id = "html_table_" . $html_table_count;
	$html_table->type = "html_table";
	$html_table->source_id = "";
	$html_table->label = "";
	$html_table->title = "";
	$html_table->content = "";
	$html_table->caption = null;
	$html_table->position = "";
	$html_table->footers = array();
	
	$nc = $xpath->query ('@id', $node);
	foreach ($nc as $n)
	{
		$html_table->source_id = $n->firstChild->nodeValue;
	}

	$nc = $xpath->query ('@position', $node);
	foreach ($nc as $n)
	{
		$html_table->position = $n->firstChild->nodeValue;
	}

	$nc = $xpath->query ('label', $node);
	foreach ($nc as $n)
	{
		$html_table->label = $n->firstChild->nodeValue;
	}
	
	// table as HTML
	$nc = $xpath->query ('table', $node);
	foreach ($nc as $n)
	{
		$html_table->content = $n->ownerDocument->saveXML($n); // where $node is your DOMNode	
	}
	
	
	$nc = $xpath->query ('caption', $node);
	foreach ($nc as $n)
	{
		
		$caption_count++;
		
		$caption = new stdclass;
		$caption->id = 'caption_' . $caption_count;
		$caption->type = "caption";
		$caption->source_id = null;
		//$caption->title = null;
		$caption->children = array();
		
		// to do
		$nc2 = $xpath->query ('p', $n);
		foreach ($nc2 as $n2)
		{
		
			$text_count++;
			$text = new stdclass;
			$text->id = "text_" . $text_count;
			$text->type = "text";
			$text->content = '';

			$paragraph_count++;
			$paragraph = new stdclass;
			$paragraph->id = "paragraph_" . $paragraph_count;
			$paragraph->type = "paragraph";
			$paragraph->children = array();
			$paragraph->children[] = $text->id;
			$paragraph->source_id = null;
	
			$text->content = $n2->nodeValue;
		
			$document->nodes->{$text->id} 		= $text;
			$document->nodes->{$paragraph->id}	= $paragraph;
	
			$caption->children[] 	= $paragraph->id;
		}
		$html_table->caption = $caption->id;
		
		$document->nodes->{$caption->id} 	= $caption;
	}
	

	
	$document->nodes->{$html_table->id} 	= $html_table;
	$document->nodes->figures->nodes[] 	= $html_table->id;

}
}


//----------------------------------------------------------------------------------------
if (1)
{
// citations

$nodeCollection = $xpath->query ('//ref-list/ref');

$citation_count = 0;

foreach ($nodeCollection as $node)
{
	//             <ref id="B6"><mixed-citation xlink:type="simple"><person-group><name name-style="western"><surname>Kress</surname> <given-names>WJ</given-names></name><name name-style="western"><surname>Prince</surname> <given-names>LM</given-names></name><name name-style="western"><surname>Williams</surname> <given-names>KJ</given-names></name></person-group> (<year>2002</year>)<article-title> The phylogeny and a new classification of the gingers (Zingiberaceae): evidence from molecular data. Amer. J. Bot.</article-title>  <volume>89</volume>: <fpage>1682</fpage>-<lpage>1696</lpage>. doi: <ext-link ext-link-type="uri" xlink:href="http://dx.doi.org/10.3732/ajb.89.10.1682" xlink:type="simple">10.3732/ajb.89.10.1682</ext-link></mixed-citation></ref>

	//$citation_count++;
	
	$article_citation = new stdclass;
	$article_citation->id = 'article_citation_' . $citation_count;
	$article_citation->type = "citation";
	$article_citation->authors = array();
	$article_citation->title = "";
	$article_citation->source = "";

	$nc = $xpath->query ('@id', $node);
	foreach ($nc as $n)
	{
		$article_citation->source_id = $n->firstChild->nodeValue;
		$article_citation->id = 'article_citation_' . str_replace('bib', '', $article_citation->source_id);
	}
	
	// publication-type="book"
	
	$nc = $xpath->query ('mixed-citation|element-citation', $node);
	foreach ($nc as $n)
	{
		$type = 'article';
		
		$nc2 = $xpath->query ('@publication-type', $n);
		foreach ($nc2 as $n2)
		{
			switch ($n2->nodeValue)
			{
				case 'book':
					$type = 'book';
					break;
					
				default:
					$type = 'article';
					break;
			}
		}
	
	
		$nc2 = $xpath->query ('article-title', $n);
		foreach ($nc2 as $n2)
		{
			// Capture all the text, ignoring formatting (e.g. italices)
			$article_citation->title = trim($n2->nodeValue);
		}
		
		// person-group
		$have_people = false;
		$nc2 = $xpath->query ('person-group/name', $n);
		foreach ($nc2 as $n2)
		{
			$have_people = true;
			
			$name = array();
			$nc3 = $xpath->query ('given-names', $n2);
			foreach ($nc3 as $n3)
			{
				$name[] = trim($n3->nodeValue);
			}
			$nc3 = $xpath->query ('surname', $n2);
			foreach ($nc3 as $n3)
			{
				$name[] = trim($n3->nodeValue);
			}
			$article_citation->authors[] = join(' ', $name);
		}
		
		// Not everybody uses 'person-group'
		if (!$have_people)
		{
			$nc2 = $xpath->query ('name', $n);
			foreach ($nc2 as $n2)
			{
				$name = array();
				$nc3 = $xpath->query ('given-names', $n2);
				foreach ($nc3 as $n3)
				{
					$name[] = trim($n3->nodeValue);
				}
				$nc3 = $xpath->query ('surname', $n2);
				foreach ($nc3 as $n3)
				{
					$name[] = trim($n3->nodeValue);
				}
				$article_citation->authors[] = join(' ', $name);
			}
		}
		

		$nc2 = $xpath->query ('source', $n);
		foreach ($nc2 as $n2)
		{
			switch ($type)
			{
				case 'book':
					$article_citation->title = trim($n2->firstChild->nodeValue);
					break;

				case 'article':
				default:
					$article_citation->source = trim($n2->firstChild->nodeValue);
					break;
			}
		}

		$nc2 = $xpath->query ('volume', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->volume = trim($n2->firstChild->nodeValue);
		}

		$nc2 = $xpath->query ('fpage', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->fpage = trim($n2->firstChild->nodeValue);
		}

		$nc2 = $xpath->query ('lpage', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->lpage = trim($n2->firstChild->nodeValue);
		}

		$nc2 = $xpath->query ('year', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->year = trim($n2->firstChild->nodeValue);
		}

		$nc2 = $xpath->query ('publisher-name', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->publisher_name = trim($n2->firstChild->nodeValue);
		}

		$nc2 = $xpath->query ('publisher-loc', $n);		
		foreach ($nc2 as $n2)
		{
			$article_citation->publisher_location = trim($n2->firstChild->nodeValue);
		}
	
		$article_citation->citation_urls = array();	

		$nc2 = $xpath->query ('ext-link/@xlink:href', $n);
		foreach ($nc2 as $n2)
		{
			//echo $n2->firstChild->nodeValue . "\n";
			
			if (preg_match('/(dx.doi.org\/)?10\./', $n2->firstChild->nodeValue))
			{
				$article_citation->doi = trim($n2->firstChild->nodeValue);
			}
			else
			{
				// this might not work as URL might be nested inside the <source> tag :(
				$article_citation->citation_urls[] = trim($n2->firstChild->nodeValue);
			}
		}
		
		
		$nc2 = $xpath->query ('pub-id[@pub-id-type="doi"]', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->doi = trim($n2->firstChild->nodeValue);
		}
		
		$nc2 = $xpath->query ('pub-id[@pub-id-type="pmid"]', $n);
		foreach ($nc2 as $n2)
		{
			$article_citation->pmid = trim($n2->firstChild->nodeValue);
			$article_citation->citation_urls["PubMed"] = 'http://www.ncbi.nlm.nih.gov/pubmed/' . $article_citation->pmid;
		}
		
		
	}


	//if ($citation_count > 5)
	{
	
	$document->nodes->citations->nodes[] = $article_citation->id;
	$document->nodes->{$article_citation->id} = $article_citation;
	
	}
	
	//print_r($article_citation );
	

}
}

//print_r($document);

header("Content-type: text/plain\n");
echo json_format(json_encode($document));

	
