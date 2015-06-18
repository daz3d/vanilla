jQuery(document).ready( function($) {
	"use strict";

	// open all external links in new window
	$('a[href^="//"]').add('a[href^="http"]').not('a[href*="' + window.location.hostname +'"]').attr('target', '_blank');

});
