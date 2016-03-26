<?php
	if (isset($_SERVER['HTTP_HTTPS']) 
		&& $_SERVER['HTTP_HTTPS'] == "on") {
		$_SERVER['HTTPS'] = "on";
	}
	require('legacy_redirect_list.php');

    $query_part = '';
    if (!empty($_SERVER['QUERY_STRING'])) {
		$query_part = '?'.$_SERVER['QUERY_STRING'];
	}

	if(isset($legacy_redirect_map[$_SERVER['DOCUMENT_URI']])){
		$dst_uri = $legacy_redirect_map[$_SERVER['DOCUMENT_URI']];
		header("Location: {$dst_uri}".$query_part,true,301);
		exit();
	}
	
	if (is_array($legacy_redir_preg)) {
		foreach ($legacy_redir_preg as $key => $redir) {
			if (preg_match('/^'.str_replace('/', '\/', $key).'/', $_SERVER['DOCUMENT_URI'])) {
				$dst_uri = $redir;
				header("Location: {$dst_uri}".$query_part,true,301);
				exit();
			}
		}
	}
	//we don't need these anymore, free the memory
	unset($legacy_redirect_map);
	unset($legacy_redir_preg);
    unset($query_part);

	require_once('index.php');

