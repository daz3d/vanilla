jQuery(document).ready( function($) {
	"use strict";

	// open all external links in new window
	$('a').not('a[href*="' + window.location.hostname +'"]').attr('target', '_blank');

});
