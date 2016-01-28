<?php if (!defined('APPLICATION')) exit();

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'bootstrap.php');

function smarty_function_dazwrapper_prehead($Params, &$Smarty) {
	if ( ! $Smarty->get_template_vars('DAZ_Wrapper')) {
		get_daz_wrapper($Smarty);
	}

	// send along any cookies from the magento side
	$header = explode("\n", str_replace(array("\r\n", "\n\r", "\r"), "\n", $Smarty->get_template_vars('DAZ_Header')));

	foreach ($header as $row) {
		if (false !== stripos($row, 'set-cookie:')) {
			// send the cookie, but DO NOT replace any previously set cookie headers
			header($row, false);
		}
	}

	$wrapperHtml = $Smarty->get_template_vars('DAZ_Wrapper');

	// from the start to just before the open <title> tag
	$header = substr($wrapperHtml, 0, strpos($wrapperHtml, '<title>'));

	return $header;
}
