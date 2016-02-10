<?php
if($this->input->is_ajax_request()){
?>
<div class="modal-wrapper column-3">
	<div class="modal-header">
		<?=$this->lang->line("error")?>
	</div>	
	<div class="modal-content">
		<div class="alert warning-message">
			<i class="typcn typcn-warning"></i>
			<?=$this->lang->line("record_not_found")?>	
		</div>
	</div>
	<div class="modal-footer">
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>
	</div>	
</div>
<?php
} else {
?>
<section class="content">
	<div class="content-inner">
		<div class="alert warning-message">
			<i class="typcn typcn-warning"></i>
			<?=$this->lang->line("record_not_found")?>	
		</div>
	</div>
</section>
<?php
}
?>