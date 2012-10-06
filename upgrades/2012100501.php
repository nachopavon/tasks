<?php
/**
 * Move text of first annotation to group forum topic object and delete annotation
 *
 * First determine if the upgrade is needed and then if needed, batch the update
 */

$tasks = elgg_get_entities_from_metadata(array(
	'type' => 'object',
	'subtype' => 'task',
	'limit' => 1,
	'metadata_name' => 'long_description',
));

// if not topics, no upgrade required
if (empty($tasks)) {
	return;
}

function tasks_2012100501($task) {
	if ($task->long_description) {
		$task->description = $task->long_description;
		$task->deleteMetadata('long_description');
		$task->save();
	}
	if ($task->parent_guid) {
		$task->list_guid = $task->parent_guid;
		$task->deleteMetadata('parent_guid');
	}
	if ($task->active) {
		$task->status = 'active';
		$task->deleteMetadata('active');
	}
	if ($task->done) {
		$task->status = 'done';
		$task->deleteMetadata('done');
	}
	return true;
}


/*
 * Run upgrade. First topics, then replies.
 */
$previous_access = elgg_set_ignore_access(true);
$options = array(
	'type' => 'object',
	'subtype' => 'task',
	'limit' => 0,
);
$batch = new ElggBatch('elgg_get_entities', $options, "tasks_2012100501", 100);
elgg_set_ignore_access($previous_access);

if ($batch->callbackResult) {
	error_log("Elgg Tasks upgrade (2012100501) succeeded");
} else {
	error_log("Elgg Tasks upgrade (2012100501) failed");
}
