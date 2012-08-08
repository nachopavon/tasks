<?php
/**
 * Create a new task
 *
 * @package ElggTasks
 */

gatekeeper();

$task_title = get_input('title');
$referer = get_input('referer');

$container_guid = (int) get_input('guid');
$container = get_entity($container_guid);
if (!$container) {
	$container = elgg_get_logged_in_user_guid();
}

elgg_set_page_owner_guid($container->getGUID());

elgg_push_breadcrumb($container->name, elgg_get_site_url() . "tasks/owner/$container->guid");

$title = elgg_echo('tasks:add');
elgg_push_breadcrumb($title);

$vars = task_prepare_form_vars();

if ($task_title) {
	$vars['title'] = $task_title;
}
if ($referer) {
	$vars['description'] = elgg_view('output/url', array(
		'href' => $referer,
		'text' => elgg_echo('tasks:this:moreinfo:here'),
	));
	$vars['description'] = elgg_echo('tasks:this:moreinfo', array($vars['description']));
}

$content = elgg_view_form('tasks/edit', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
