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

// GBIF examples
$urls = array(
'http://www.gbif.org/occurrence/317834729'

);

// Piranha
$urls = array(
'http://www.gbif.org/occurrence/999838402',
'http://www.gbif.org/occurrence/624171312'

);

// BOLD
// BOLD, direct from BOLD and equivalent GBIF record
$urls = array(
'http://bins.boldsystems.org/index.php/Public_RecordView?processid=ASANQ054-09',
'http://www.gbif.org/occurrence/1291693678'
);

$urls = array(
'http://www.gbif.org/occurrence/624171316'
);

// Piranha, GBIF museum, GBIF EMBL, GenBank
$urls = array(
'http://www.gbif.org/occurrence/999838402',
'http://www.gbif.org/occurrence/624171312',
'http://www.ncbi.nlm.nih.gov/nuccore/146428523'
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
