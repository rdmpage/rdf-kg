<?php


//--------------------------------------------------------------------------------------------------
// Clean up text so that we have single spaces between text, 
// see https://github.com/readmill/API/wiki/Highlight-locators
function clean_text($text)
{
	define ('WHITESPACE_CHARS', ' \f\n\r\t\x{00a0}\x{0020}\x{1680}\x{180e}\x{2028}\x{2029}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200a}\x{202f}\x{205f}\x{3000}');
	
	$text = preg_replace('/[' . WHITESPACE_CHARS . ']+/u', ' ', $text);
	
	return $text;
}


//--------------------------------------------------------------------------------------------------
// Add an annotation 
function new_annotation(&$document, $type, $store = true)
{
	if (!isset($document->node_type_counter[$type]))
	{
		$document->node_type_counter[$type] = 0;
	}
	$document->node_type_counter[$type]++;
	$id = $type . '_' . $document->node_type_counter[$type];
	$document->nodes->{$id} = new stdclass;
	$document->nodes->{$id}->type = $type;
	$document->nodes->{$id}->id = $id;
	
	$document->nodes->{$id}->range = array();
	$document->nodes->{$id}->range[0] = $document->counter;
	
	$document->nodes->{$id}->path = array();
	$document->nodes->{$id}->path[0] = $document->current_paragraph_node->id;
	$document->nodes->{$id}->path[1] = 'content';
	
	if ($store)
	{
		$document->current_node[] = $document->nodes->{$id};
	}
	
	return $document->nodes->{$id};
}

//--------------------------------------------------------------------------------------------------
// Store text span that annotation applies to
function add_annotation(&$document, $annotation)
{
	if (!isset($document->current_paragraph_node->open_annotation[$annotation->range[0]]))
	{
		$document->current_paragraph_node->open_annotation[$annotation->range[0]] = array();
	}
	
	if (!isset($document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]]))
	{
		$document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]] = array();
	}
	$document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]][] = $annotation->id;

	krsort($document->current_paragraph_node->open_annotation[$annotation->range[0]], SORT_NUMERIC);
	
	asort($document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]]);
	
	if (!isset($document->current_paragraph_node->close_annotation[$annotation->range[1]]))
	{
		$document->current_paragraph_node->close_annotation[$annotation->range[1]] = array();
	}
	
	if (!isset($document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]]))
	{
		$document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]] = array();
	}
	
	$document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]][] = $annotation->id;
	
	ksort($document->current_paragraph_node->close_annotation[$annotation->range[1]], SORT_NUMERIC);
	arsort($document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]]);
}

//--------------------------------------------------------------------------------------------------
// A page, which may contain the entire article, or a single page
function create_page_node(&$document)
{
	if (!isset($document->node_type_counter['page']))
	{
		$document->node_type_counter['page'] = 0;
	}
	$document->node_type_counter['page']++;
	
	$id = 'page_' . $document->node_type_counter['page'];
	$document->nodes->{$id} = new stdclass;
	$document->nodes->{$id}->type = 'page';
	$document->nodes->{$id}->id = $id;	
	$document->nodes->{$id}->children = array();
	
	
	$document->current_page_node = $document->nodes->{$id};
}

//--------------------------------------------------------------------------------------------------
function check_annotation_not_overlapping(&$document, $annotation)
{
	$ok = true;
	
	if (0)
	{
		echo '<pre>';
		print_r($document->added_annotations[$document->current_paragraph_node->id]);
		echo "Annotation<br/>";
		print_r($annotation->range);
		echo '</pre>';
	}
	
	if (isset($document->added_annotations[$document->current_paragraph_node->id]))
	{
		// check for overlap/subset
		$ok = true;
		$n = count($document->added_annotations[$document->current_paragraph_node->id]);
		while ($ok && $n > 1)
		{
			$n--;
			
			if (0)
			{
				echo "<pre>";
				echo "Checking...<br/>";
				print_r($annotation->range);
				print_r($document->added_annotations[$document->current_paragraph_node->id][$n]);
			}
			
			$ok = (
			($annotation->range[0] < $document->added_annotations[$document->current_paragraph_node->id][$n][0])
			|| ($annotation->range[0] > $document->added_annotations[$document->current_paragraph_node->id][$n][1])
			);
			
			if (0)
			{
				if ($ok)
				{
					echo '<b>OK</b>';
				} 
				else
				{
					echo '<b>bugger</b>';
				}
				
				echo '</pre>';
			}
		}
	}
	else
	{
		$document->added_annotations[$document->current_paragraph_node->id] = array();
	}
	
	return $ok;
}

//--------------------------------------------------------------------------------------------------
// Recursively traverse HTML DOM and process tags
function dive($node, &$document )
{

	echo $node->nodeName . "\n";

	switch ($node->nodeName)
	{
		case 'p':
			if (!isset($document->node_type_counter['p']))
			{
				$document->node_type_counter['p'] = 0;
			}
			$document->node_type_counter['p']++;
			
			$document->counter = 0;
			
			$id = 'paragraph_' . $document->node_type_counter['p'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'paragraph';
			$document->nodes->{$id}->id = $id;
			$document->nodes->{$id}->children=array();
			$document->nodes->{$id}->content = '';			
			
			// HTML attributes
			if ($node->hasAttributes()) 
			{ 
				$attributes = $node->attributes; 
				
				foreach ($attributes as $attribute)
				{
					switch ($attribute->name)
					{
						case 'style':
							$document->nodes->{$id}->style = $attribute->value;
							break;
							
						default:
							break;
					}
				}
			}			
			
			// support for annotations
			$document->nodes->{$id}->open_annotation = array();
			$document->nodes->{$id}->close_annotation = array();
						
			$document->current_node[] = $document->nodes->{$id};
			$document->current_paragraph_node = $document->nodes->{$id};
			
			// add paragraph to current page
			if (!$document->current_page_node)
			{
				create_page_node($document);
			}
			
			$document->current_page_node->children[] = $id;
			break;
			
		case 'img':
			if (!isset($document->node_type_counter['figure']))
			{
				$document->node_type_counter['figure'] = 0;
			}
			$document->node_type_counter['figure']++;
			
			$id = 'figure_' . $document->node_type_counter['figure'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'figure';
			$document->nodes->{$id}->id = $id;
			
			// HTML attributes
			if ($node->hasAttributes()) 
			{ 
				$attributes = $node->attributes; 
				
				foreach ($attributes as $attribute)
				{
					switch ($attribute->name)
					{
						case 'src':
							$document->nodes->{$id}->url = $attribute->value;
							break;
							
						default:
							break;
					}
				}
			}
			break;
			
		
		case 'i':
			new_annotation($document, 'emphasis');
			break;
			
		case 'b':
			new_annotation($document, 'strong');
			break;

		case 'br':
			new_annotation($document, 'linebreak');
			break;

		case 'sup':
			new_annotation($document, 'superscript');
			break;
			
		case 'wbr':
			new_annotation($document, 'softhyphen');
			break;			
						
		case '#text':
			// Grab text and clean it up
			
			/*
			if (!isset($document->node_type_counter['text']))
			{
				$document->node_type_counter['text'] = 0;
			}
			$document->node_type_counter['text']++;
			
			$id = 'text_' . $document->node_type_counter['text'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'text';
			$document->nodes->{$id}->id = $id;	
			*/
		
			$content = $node->nodeValue;
			
			// clean text 
			$content = clean_text($content);
			
			// very important!
			$content_length =  mb_strlen($content, mb_detect_encoding($content));
			
			if (!isset($document->current_paragraph_node))
			{
				$document->current_paragraph_node = new stdclass;
			}
		
			$document->current_paragraph_node->content .= $content;
			$document->counter += $content_length;
			
			// text node
			/*
			$document->nodes->{$id}->content = $content;
			$document->current_node[] = $document->nodes->{$id};
			
			$document->current_paragraph_node->children[] = $id;
			*/
			break;	
						
		default:
			// a tag we don't handle, just record for now
			if (!isset($document->node_type_counter['unknown']))
			{
				$document->node_type_counter['unknown'] = 0;
			}
			$document->node_type_counter['unknown']++;
			$id = 'unknown' . $document->node_type_counter['unknown'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'unknown';
			$document->nodes->{$id}->id = $id;
			$document->nodes->{$id}->name = $node->nodeName;
			
			$document->current_node[] = $document->nodes->{$id};
		
			break;
	}
	
	// Visit any children of this node
	if ($node->hasChildNodes())
	{
		foreach ($node->childNodes as $children) {
			dive($children, $document);
		}
	}
	
	// Leaving this node, any annotations that cover a span of text get closed here
	// This is also the point at which we have all the text for a paragraph node, so
	// do any entity recognition here
	$n = array_pop($document->current_node);
	
	switch ($n->type)
	{
		// handle formatting annotations that span a range of text
		case 'emphasis':
		case 'strong':
		case 'superscript':
			$n->range[1] = max(0, $document->counter - 1);
			$n->path[0] = $document->current_paragraph_node->id;
			
			// These annotations are spans that have open and closing tags
			add_annotation($document, $n);			
			break;
			
		// formatting that is a closed tag with no text content , e.g. <wbr/> or <br/>
		case 'linebreak':
		case 'softhyphen':
			$n->range[1] = $document->counter;
			$n->path[0] = $document->current_paragraph_node->id;
			add_annotation($document, $n);			
			break;

		case 'paragraph':
		
			/*
			
			// leaving paragraph node, do any entity recognition here
			
			
			// identifiers
			find_identifiers($document);
			

			// names
			//find_taxon_names($document);
						
			// georeferenced points
			find_latlong($document);
			
			// specimen codes
			find_specimen_codes($document);
					
			// GenBank
			find_genbank($document);
			
			// citations
			
			// other entities
			
			*/
			
			
			break;
			
		default:
			break;
	}
}

$filename = 'phytokeys-1426.xml';

$xml = file_get_contents($filename);

$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
	
	$document = new stdclass;
	$document->nodes = new stdclass;
	
	// house keeping
	$document->counter = 0;
	$document->node_type_counter = array();
	$document->current_paragraph_node = null;
	$document->current_text_node = null;
	$document->current_page_node = null;
	
	$document->added_annotations = array();
	
	
	
	$counter = 0;
	foreach ($dom->documentElement->childNodes as $node) {
		echo $node->nodeName;
		dive($node, $document); 
	}
	
	
	// remove housekeeping
	unset($document->counter);
	unset($document->node_type_counter);
	unset($document->current_paragraph_node);
	unset($document->current_text_node);
	unset($document->current_page_node);
	unset($document->current_node);
	
	// to do 
	unset($document->added_annotations);
	
	print_r($document);

?>
