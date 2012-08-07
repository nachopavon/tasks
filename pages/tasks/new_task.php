<?php
/**
 * Create a new task
 *
 * @package ElggTasks
 */

gatekeeper();

$parent_guid = (int) get_input('parent_guid');
$parent = get_entity($parent_guid);
if (!$parent) {

}

if (elgg_instanceof($parent, 'object', 'tasklist')) {
	$container = $parent->getContainerEntity();
} else {
	$container = $parent;
	$parent = false;
	$parent_guid = 0;
}

elgg_set_page_owner_guid($container->getGUID());

if (elgg_instanceof($container, 'user')) {
	elgg_push_breadcrumb($container->name, "tasks/owner/$container->username");
} else {
	elgg_push_breadcrumb($container->name, "tasks/group/$container->guid/all");
}

if ($parent) {
	elgg_push_breadcrumb($parent->title, $parent->getURL());
}

$title = elgg_echo('tasks:add');
elgg_push_breadcrumb($title);

$vars = task_prepare_form_vars(null, $parent_guid);
$content = elgg_view_form('tasks/edit', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
