<?php if (!defined('APPLICATION')) exit();

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'bootstrap.php');

function smarty_function_dazwrapper_foot($Params, &$Smarty) {
	if ( ! $Smarty->get_template_vars('DAZ_Wrapper')) {
		get_daz_wrapper($Smarty);
	}

	$wrapperHtml = $Smarty->get_template_vars('DAZ_Wrapper');

	// from the start of the footer to the end
	$footer = substr($wrapperHtml, strpos($wrapperHtml, '[[[ THIS PAGE INTENTI0NALLY LEFT BLANK ]]]') + strlen('[[[ THIS PAGE INTENTI0NALLY LEFT BLANK ]]]'));

	return $footer;
}
