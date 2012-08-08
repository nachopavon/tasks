<?php
/*
 * 
 */
?>


$(function(){
	$('.elgg-menu-extras .elgg-menu-item-task a').click(function(){
		var title = encodeURIComponent($('h2.elgg-heading-main').text());
		$(this).attr('href', $(this).attr('href') + "&title=" + title);
	});
});