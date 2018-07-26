<?php

if ( ! function_exists('get_daz_wrapper')) {
	function get_daz_wrapper(& $Smarty) {
		$ch = curl_init(Gdn::Config('Daz.RootUrl') . Gdn::Config('Daz.WrapperUrl'));

		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response = curl_exec($ch);

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		curl_close($ch);

		$Smarty->assign('DAZ_Header', $header);
		$Smarty->assign('DAZ_Wrapper', $body);
	}
}
