<?php
function includeStylesheet(&$CI){
	return array();
}

function includeScript(&$CI){
	return 
		array(
			$CI->theme->getThemeUrl()."js/green.notifications.js"
		);
}

function includeCustomScript(&$CI){
	return array();
}
?>