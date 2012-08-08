<?php

$container_guid = get_input('container_guid', elgg_get_page_owner_guid());

$entities = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'tasklist_top',
	'container_guid' => $container_guid,
	'limit' => 0,
));

$options_values = array();
foreach ($entities as $entity) {
	$options_values[$entity->guid] = $entity->title;
}

echo elgg_view('input/dropdown', array(
	'name' => $vars['name'],
	'options_values' => $options_values,
	'value' => $vars['value'],
));
