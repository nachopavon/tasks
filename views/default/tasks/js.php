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

$(function() {
	if($.colorbox) {
		$('.elgg-menu-title .elgg-menu-item-subtask a').attr('href', '#tasks-inline-form').colorbox({
			inline: true,
			'onLoad': function() {$('#tasks-inline-form').show()},
			'onComplete': function() {$('.elgg-autofocus').focus()},
			'onClosed': function() {$('#tasks-inline-form').hide();$(this).blur()},
			'width': '50%',
		});
		$('#tasks-inline-form').submit(function() {
			$.colorbox.close();
			var values = {};
			$.each($(this).serializeArray(), function(i, field) {
				values[field.name] = field.value;
			});
			elgg.action($(this).attr('action'), {
				data: values,
				success: function(json) {
					var listview = $('.elgg-module-info h3:contains(' + elgg.echo('tasks:unassigned') + ')').parent().parent();
					var tasklist_graph = $('.tasklist-graph').parent();
					elgg.get({
						url: elgg.config.wwwroot + "ajax/view/object/task",
						dataType: "html",
						cache: false,
						data: {
							guid: json.output.guid,
						},
						success: function(htmlData) {
							if (htmlData.length > 0) {
								htmlData = '<li class="elgg-item" id="elgg-object-'
												+ json.output.guid + '">' + htmlData + '</li>';
								
								if (listview.find('.elgg-list-entity').length > 0) {
									listview.find('.elgg-list-entity').prepend(htmlData)
								} else {
									$('<ul class="elgg-list elgg-list-entity">').append(htmlData).appendTo(listview.show());
								}
							}
						}
					});
					elgg.get({
						url: elgg.config.wwwroot + "ajax/view/tasks/tasklist_graph",
						dataType: "html",
						cache: false,
						data: {
							guid: json.output.list_guid,
						},
						success: function(htmlData) {
							if (htmlData.length > 0) {
								tasklist_graph.html(htmlData);
							}
						}
					});
				}
			});
			this.reset();
			return false;
		});
	}
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