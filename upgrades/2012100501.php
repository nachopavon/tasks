<?php
/**
 * Move text of first annotation to group forum topic object and delete annotation
 *
 * First determine if the upgrade is needed and then if needed, batch the update
 */

$tasks = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'tasks',
	'limit' => 1,
));

// if not topics, no upgrade required
if (empty($tasks)) {
	return;
}

function tasks_2012100501($task) {
	require_once(elgg_get_plugins_path() . 'upgrade-tools/lib/upgrade_tools.php');
	if ($task->long_description) {
		$task->description = $task->long_description;
		$task->deleteMetadata('long_description');
		$task->save();
	}
	if ($task->parent_guid) {
		$task->list_guid = $task->parent_guid;
		$task->deleteMetadata('parent_guid');
	}
	else {
		$task->list_guid = 0;
	}
	if ($task->active) {
		$task->status = 'active';
		$task->deleteMetadata('active');
	}
	if ($task->done) {
		$task->status = 'done';
		$task->deleteMetadata('done');
	}
	upgrade_change_subtype($task, 'task');
	return true;
}


/*
 * Run upgrade. First topics, then replies.
 */
$previous_access = elgg_set_ignore_access(true);
$options = array(
	'type' => 'object',
	'subtype' => 'tasks',
	'limit' => 0,
);
$batch = new ElggBatch('elgg_get_entities', $options, "tasks_2012100501", 100);
elgg_set_ignore_access($previous_access);

if ($batch->callbackResult) {
	error_log("Elgg Tasks upgrade (2012100501) succeeded");
} else {
	error_log("Elgg Tasks upgrade (2012100501) failed");
}
