jQuery(document).ready( function($) {

	"use strict";

	// open all external links in new window
	$('a[href^="//"]').add('a[href^="http"]').not('a[href*="' + window.location.hostname +'"]').attr('target', '_blank');

	// if user got here via direct post link, scroll up a bit to bring full post back into view
	if (1 === document.location.hash.indexOf('Comment')) {
		$('body').scrollTop($('body').scrollTop( ) + 120);
	}

});
