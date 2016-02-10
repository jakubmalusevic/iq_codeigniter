<?php
if (!$this->acl->isGuest()) {
	$this->navigation->drawNavigation(false,true,$active_module);
}
?>