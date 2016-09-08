<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Server-------------------------------------------------------------------------------------------
$config['server'] 		= 'http://localhost/~rpage/triplestore';


// Database-----------------------------------------------------------------------------------------
$config['adodb_dir'] 	= dirname(__FILE__).'/adodb5/adodb.inc.php'; 
$config['db_user'] 	    = 'root';
$config['db_passwd'] 	= '';
$config['db_name'] 	    = 'rdf_kg';


// Directories--------------------------------------------------------------------------------------

// Proxy settings for connecting to the web---------------------------------------------------------

// Set these if you access the web through a proxy server. This
// is necessary if you are going to use external services such
// as PubMed.
$config['proxy_name'] 	= '';
$config['proxy_port'] 	= '';

//$config['proxy_name'] 	= 'wwwcache.gla.ac.uk';
//$config['proxy_port'] 	= '8080';

?>