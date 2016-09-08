<?php

/**
 * @file reset.php
 *
 * Reset the triple store (this will wipe your data!)
 *
 */

require_once (dirname(__FILE__) . '/triple_store.php');

// Triple store
global $store_config;

$r = $store->reset();

?>
