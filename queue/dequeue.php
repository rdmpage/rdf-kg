<?php

// Dequeue some objects

require(dirname(__FILE__) . '/queue.php');

//dequeue(100, true);


while (!queue_is_empty())
{
	dequeue(100);
}


?>
