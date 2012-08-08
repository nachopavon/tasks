<?php
/*
 * 
 */
?>

elgg.provide('elgg.ui.getSelection');

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

$(function(){
	$('.elgg-menu-extras .elgg-menu-item-task a').click(function(){
		var title = encodeURIComponent(elgg.ui.getSelection());
		if (!title) {
			title = encodeURIComponent($('h2.elgg-heading-main').text());
		}
		$(this).attr('href', $(this).attr('href') + "&title=" + title);
	});
});