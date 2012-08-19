<?php
/**
 * View a single task
 *
 * @package ElggTasks
 */

$guid = get_input('guid');
$entity = get_entity($guid);
if (!$entity) {
	forward();
}

$container = $entity->getContainerEntity();
$list = get_entity($entity->list_guid);

elgg_set_page_owner_guid($container->guid);

group_gatekeeper();

if (!$container) {
}

$title = $entity->title;

if (elgg_instanceof($container, 'user')) {
	elgg_push_breadcrumb($container->name, "tasks/owner/$container->username/");
} elseif (elgg_instanceof($container, 'group')) {
	elgg_push_breadcrumb($container->name, "tasks/group/$container->guid/all");
}
if($list) {
	elgg_push_breadcrumb($list->title, $list->getURL());
}
elgg_push_breadcrumb($title);

$content = elgg_view_entity($entity, array('full_view' => true));

if (!$list && $entity->canEdit()) {
	
	elgg_load_js('lightbox');
	elgg_load_css('lightbox');
	
	$url = "tasks/add/$entity->guid";
	elgg_register_menu_item('title', array(
			'name' => 'subtask',
			'href' => $url,
			'text' => elgg_echo('tasks:newchild'),
			'link_class' => 'elgg-button elgg-button-action',
	));
	
} else {
	$content .= elgg_view_comments($entity);
}

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('tasks/sidebar/navigation', array('entity' => $entity)),
));

echo elgg_view_page($title, $body);
