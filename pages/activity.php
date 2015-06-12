<?php
gatekeeper();

$user = elgg_get_logged_in_user_entity();

elgg_set_page_owner_guid($user->guid);

$db_prefix = elgg_get_config('dbprefix');
$activity = elgg_list_river(array(
	'joins' => array("JOIN {$db_prefix}entities object ON object.guid = rv.object_guid"),
	'wheres' => array("
		rv.subject_guid = $user->guid
		OR rv.subject_guid IN (SELECT guid_two FROM {$db_prefix}entity_relationships WHERE guid_one=$user->guid AND relationship='follower')
		OR rv.subject_guid IN (SELECT guid_one FROM {$db_prefix}entity_relationships WHERE guid_two=$user->guid AND relationship='friend')
	"),
	'limit' => 1,
	'pagination' => false,
));


echo $activity;