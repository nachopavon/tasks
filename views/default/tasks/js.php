<?php
/*
 * 
 */
?>

elgg.provide('elgg.ui.getSelection');
elgg.provide('elgg.tasks');

elgg.ui.getSelection = function () {
	if (window.getSelection) {
		return window.getSelection().toString();
	}
	else if (document.getSelection) {
		return document.getSelection();
	}
	else if (document.selection) {
		// this is specifically for IE
		return document.selection.createRange().text;
	}
}

elgg.tasks.newTask = function(event) {
	var values = {};
	$.each($(this).serializeArray(), function(i, field) {
		values[field.name] = field.value;
	});
	elgg.action($(this).attr('action'), {
		data: values,
		success: function(json) {
			var unassignedlist = $('#tasks-status-unassigned').parent().parent();
			elgg.tasks.insert(json.output.guid, unassignedlist);
			elgg.tasks.updateTaskGraph();
		}
	});
	this.reset();
	$(this).slideUp();
	event.preventDefault();
}

elgg.tasks.updateTaskGraph = function() {
	var tasklist_graph = $('.tasklist-graph').parent();
	var guid = parseInt(window.location.href.substr(elgg.config.wwwroot.length + 'tasks/view/'.length));
	elgg.get({
		url: elgg.config.wwwroot + "ajax/view/tasks/tasklist_graph",
		dataType: "html",
		cache: false,
		data: {
			guid: guid,
		},
		success: function(htmlData) {
			if (htmlData.length > 0) {
				tasklist_graph.html(htmlData);
			}
		}
	});
}

elgg.tasks.insert = function(guid, list) {
	elgg.get({
		url: elgg.config.wwwroot + "ajax/view/object/task",
		dataType: "html",
		cache: false,
		data: {
			guid: guid,
		},
		success: function(htmlData) {
			if (htmlData.length > 0) {
				htmlData = '<li class="elgg-item" id="elgg-object-'
								+ guid + '">' + htmlData + '</li>';

				if (list.find('.elgg-list-entity').length > 0) {
					list.find('.elgg-list-entity').prepend(htmlData)
				} else {
					$('<ul class="elgg-list elgg-list-entity">').append(htmlData).appendTo(list.show());
				}
			}
		}
	});
}

elgg.tasks.changeStatus = function(event) {
	var action = $('a', this).attr('href');
	var guid = (new RegExp('[\\?&]entity_guid=([^&#]*)').exec(action))[1];
	elgg.action(action, {
		success: function(json) {
			switch (json.output.new_state) {
				case 'assigned':
				case 'active':
					var list = 'assigned';
					break;
				case 'new':
				case 'unassigned':
				case 'reopened':
					var list = 'unassigned';
					break;
				case 'done':
				case 'closed':
					var list = 'closed';
					break;
			}
			var newlist = $('#tasks-status-' + list);
			$('#elgg-object-' + guid).remove();
			elgg.tasks.insert(guid, newlist);
			elgg.tasks.updateTaskGraph();
		}
	});
	event.preventDefault();
};

$(function() {
	$('.elgg-menu-title .elgg-menu-item-subtask a').click(function(event) {
		$('#tasks-inline-form')
			.slideToggle()
			.find('[name="title"]').focus();
		event.preventDefault();
	});
	
	$('#tasks-inline-form').submit(elgg.tasks.newTask);
	
	$('body').delegate('.elgg-menu-tasks-hover li', 'click', elgg.tasks.changeStatus);
	
	$('.elgg-menu-extras .elgg-menu-item-task a').click(function() {
		var title = encodeURIComponent(elgg.ui.getSelection());
		if (!title) {
			title = encodeURIComponent($('h2.elgg-heading-main').text());
		}
		referer_guid = $('.elgg-form-comments-add input[name="entity_guid"]').val();
		var href = $(this).attr('href') + "&title=" + title;
		if (referer_guid) {
			href += "&referer_guid=" + referer_guid;
		}
		$(this).attr('href', href);
	});
});