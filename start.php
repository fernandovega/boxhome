<?php
/**
 * Elgg composer plus plugin
 * 
 */

elgg_register_event_handler('init', 'system', 'composer_init');

function composer_init() {

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('composer', 'composer_page_handler');

	// Extend system CSS with our own styles
	elgg_extend_view('css/elgg', 'composer/css');

  $ctx = elgg_get_context();

  if ($ctx == 'activity'){
    elgg_extend_view('page/layouts/elements/header', 'composer/init');
  } 
  
  //elgg_extend_view('js/elgg', 'composer/js/init.composer.js');
}

function composer_page_handler($page) {

	if (!isset($page[0])) {
		$page[0] = 'activity';
	}

	$composer_dir = elgg_get_plugins_path() . 'composer/pages';

	$page_type = $page[0];
	switch ($page_type) {
		case 'activity':			
			include "$composer_dir/activity.php";
			break;
		default:
			return false;
	}
	return true;
}