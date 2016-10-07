<?php

// Manage a queue of objects

// Queue is managed by views in CouchDB


require_once(dirname(dirname(__FILE__)) . '/documentstore/couchsimple.php');
require_once(dirname(dirname(__FILE__)) . '/resolvers/resolve.php');


//----------------------------------------------------------------------------------------
// Put an item in the queue 
function enqueue($url)
{
	global $config;
	global $couch;
	
	// Check whether this URL already exists (have we done this object already?)
	// to do: what about having multiple URLs for same thing, check this
	$exists = $couch->exists($url);

	if (!$exists)
	{
		$doc = new stdclass;
		
		// URL is document id and also source (i.e., we will resolve this URL to get details on object)
		$doc->_id = $url;	
		
		// By default message is empty and has timestamp set to now
		// This means it will be at the bottom of the queue of things to add
		$doc->{'message-timestamp'} = date("c", time());
		$doc->{'message-modified'} 	= $doc->{'message-timestamp'};
		$doc->{'message-format'} 	= 'unknown';
		
		$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($doc->_id), json_encode($doc));
		var_dump($resp);
	}
	else
	{
		echo "Exists\n";
	}
}

//----------------------------------------------------------------------------------------
function queue_is_empty()
{
}

//----------------------------------------------------------------------------------------
// Item is a single row from a CouchDB query
function fetch($item)
{
	global $config;
	global $couch;
	
	// log
	echo "Resolving " . $item->value . "\n";
	//exit();
	
	$data = null;
	$data = resolve_url($item->value);
	
	print_r($data);
	
	if ($data)
	{
		// if we have message content, update object with that message, which will remove it from the queue
		// Assuming we have set {'message-format'} to one of the MIME types recognised by the CouchDB
		// views, the object will also be indexed by the corresponding view
		if (isset($data->message))
		{
			// Think about how many, if any, links from this item we put in the queue
			
			// Think about how many, if any, links from this item we put in the queue
			// add any links in this object to the queue
			if (isset($data->links))
			{
				$add_links = true;
				
				if (preg_match('/worldcat.org/', $item->value))
				{
					$add_links = false;
				}
				
				if ($add_links)
				{
					foreach ($data->links as $link)
					{
						// log
						echo "Adding " . $link . " to queue\n";

						enqueue($link);
					}
				}
			}			
			
			// update document store item with message content
			$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($item->value));
			var_dump($resp);
			if ($resp)
			{
				$doc = json_decode($resp);
				if (!isset($doc->error))
				{
					$doc->{'message-modified'} = date("c", time());					
					$doc->{'message-format'} = $data->{'message-format'};
					$doc->message = $data->message;
					
					$resp = $couch->send("PUT", "/" . $config['couchdb_options']['database'] . "/" . urlencode($doc->_id), json_encode($doc));
					var_dump($resp);
				}
			}	
		}		
	}
	
	
	
	
	// push item to Neo4J, triple store, or whateever database we are using  (or do we let the changes API handle this?)
	
}

//----------------------------------------------------------------------------------------
// Dequeue one or more objects and fetch them
// 
// to do: if we get just one object, and that fails, we may end up with a queue that is 
// forever stuck, so maybe get a bunch of items, and resolve those.
function dequeue($n = 5, $descending = false)
{
	global $config;
	global $couch;
	
	$url = '_design/queue/_view/todo?limit=' . $n;
	
	if ($descending)
	{
		$url .= "&descending=true";
	}
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	$response_obj = json_decode($resp);

	print_r($response_obj);
		
	// fetch content
	$count = 0;
	foreach ($response_obj->rows as $row)
	{
		fetch($row);	
		
		// Give source a rest
		if (($count++ % 10) == 0)
		{
			$rand = rand(1000000, 3000000);
			echo '...sleeping for ' . round(($rand / 1000000),2) . ' seconds' . "\n";
			usleep($rand);
		}
		
	}
		
}

//----------------------------------------------------------------------------------------
// Load one item directly into database without waiting for it to be dequeued
function load_url($url)
{
	// Ensure item is in the queue 
	enqueue($url);
	
	// simulate the result of a CouchDB query by creating an item that has
	// the URL to resolve as it's value
	$item = new stdclass;
	$item->value = $url;
	// fetch the item
	fetch($item);
}


dequeue(100);

//enqueue('http://dx.doi.org/10.1371/journal.pone.0133602');
//dequeue(5, true);

//load_url('http://www.ncbi.nlm.nih.gov/pubmed/17148433');

//enqueue('http://dx.doi.org/10.1371/journal.pone.0133602');
//dequeue(5, true);


//enqueue('http://dx.doi.org/10.7554/eLife.08347');
//dequeue(20, true);

//enqueue('http://dx.doi.org/10.7554/eLife.08347');
//dequeue(100);


/*
enqueue('http://dx.doi.org/10.11646/phytotaxa.208.1.1');
// ORCID but not in CrossRef metadata
enqueue('http://dx.doi.org/10.1111/j.1756-1051.2000.tb00727.x');
// ORCIDs in CrossRef metadata
enqueue('http://dx.doi.org/10.7554/eLife.08347');
*/

//enqueue('http://orcid.org/0000-0002-7573-096X');

//dequeue(10, true);

// eLife article
//enqueue('http://dx.doi.org/10.7554/eLife.08347');

// eLife article author
//enqueue('http://orcid.org/0000-0001-8916-5570');

// Journal with ISSN
//enqueue('http://www.worldcat.org/issn/0075-5036');

// Phytotaxa 2015
//enqueue('http://dx.doi.org/10.11646/phytotaxa.208.1.1');
//enqueue('http://dx.doi.org/10.11646/phytotaxa.227.1.9');
//enqueue('http://dx.doi.org/10.1186/s40529-015-0087-5');
//enqueue('http://dx.doi.org/10.11646/phytotaxa.222.2.1');

// Force load
//$url = 'http://www.ncbi.nlm.nih.gov/nucore/359280095';
//load_url($url);

/*
// Normal operation
enqueue(x);
dequeue();
*/


?>
