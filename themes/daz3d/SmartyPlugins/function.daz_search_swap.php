<?php if (!defined('APPLICATION')) exit();

function smarty_function_daz_search_swap($Params, & $Smarty) {
	$url = Url('/search');
	$script = <<< EOS

<script type="text/javascript">
	jQuery(document).ready( function($) {
		// change to forum search
		$('.search_mini_form')
			.attr('action', '{$url}')
			.off('submit')
			.find('input[name="q"]')
				.attr('name', 'Search')
				.attr('placeholder', 'Search Forums')
				.off('keydown keyup keypress blur');
	});
</script>

EOS;

	return $script;
}
