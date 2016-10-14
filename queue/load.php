<?php

// Add some objects

require(dirname(__FILE__) . '/queue.php');

$urls = array(
'http://www.ncbi.nlm.nih.gov/pubmed/27058864'
);

// A new blue-tailed Monitor lizard (Reptilia, Squamata, Varanus) of the Varanus indicus group from Mussau Island, Papua New Guinea.
$urls = array(
'http://www.ncbi.nlm.nih.gov/pubmed/27103877'
);

$force = false;
$force = true;

foreach ($urls as $url)
{
	enqueue($url, $force);
}
	
while (!queue_is_empty())
{
	dequeue(100);
}


?>
