var lang={};
<?php
foreach($this->lang->language as $line=>$translation) {
?>
lang['<?=addslashes($line)?>']='<?=addslashes($translation)?>';
<?php
}
?>