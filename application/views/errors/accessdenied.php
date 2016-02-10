<?php
if($this->input->is_ajax_request()){
?>
<div class="modal-wrapper column-3">
	<div class="modal-header">
		<?=$this->lang->line("error")?>
	</div>	
	<div class="modal-content">
		<?php
		$this->notifications->drawNotifications();
		?>
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
		<?php
		$this->notifications->drawNotifications();
		?>		
	</div>
</section>
<?php
}
?>