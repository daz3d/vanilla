<?php if (!defined('APPLICATION')) exit();

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'bootstrap.php');

function smarty_function_dazwrapper_posthead($Params, &$Smarty) {
	if ( ! $Smarty->get_template_vars('DAZ_Wrapper')) {
		get_daz_wrapper($Smarty);
	}

	$wrapperHtml = $Smarty->get_template_vars('DAZ_Wrapper');

	// remove the magento #crumbs div
	$dom = new DOMDocument;
	libxml_use_internal_errors(true); // PHP DOM doesn't like HTML5
	$dom->loadHTML($wrapperHtml);
	libxml_clear_errors( );
	$xPath = new DOMXPath($dom);
	$nodes = $xPath->query('//*[@id="crumbs"]');
	if ($nodes->item(0)) {
		$nodes->item(0)->parentNode->removeChild($nodes->item(0));
	}
	$wrapperHtml = $dom->saveHTML( );

	// from the close </title> tag to the end of the header
	$start = strpos($wrapperHtml, '</title>') + strlen('</title>');
	$length = strpos($wrapperHtml, '[[[ THIS PAGE INTENTI0NALLY LEFT BLANK ]]]') - $start;
	$header = substr($wrapperHtml, $start, $length);

	return $header;
}
