<?php
function includeStylesheet(&$CI){
	return array();
}

function includeScript(&$CI){
	return 
		array(
			$CI->theme->getThemeUrl()."js/red.notifications.js"
		);
}

function includeCustomScript(&$CI){
	return array();
}
?>