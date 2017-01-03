<?php
/**
 * Elgg boxhome plus plugin
 * 
 */

elgg_register_event_handler('init', 'system', 'boxhome_init');

function boxhome_init() {

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('boxhome', 'boxhome_page_handler');

	// Extend system CSS with our own styles
	elgg_extend_view('css/elgg', 'boxhome/css');

  $ctx = elgg_get_context();

  if ($ctx == 'activity'){
    elgg_extend_view('page/layouts/elements/header', 'boxhome/init');
  } 
  
  //elgg_extend_view('js/elgg', 'boxhome/js/init.boxhome.js');
}

function boxhome_page_handler($page) {

	if (!isset($page[0])) {
		$page[0] = 'activity';
	}

	$boxhome_dir = elgg_get_plugins_path() . 'boxhome/pages';

	$page_type = $page[0];
	switch ($page_type) {
		case 'activity':			
			include "$boxhome_dir/activity.php";
			break;
		default:
			return false;
	}
	return true;
}