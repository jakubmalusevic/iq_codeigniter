<div class="modal-wrapper column-4">
	<div class="modal-header">
		<?=$this->lang->line("update_engine_version_to")." v.".$version_info->version?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<?=$version_info->version_info?>
		</div>
		<div class="form-error-handler" error-handler="true"></div>
	</div>	
	<div class="modal-footer">
		<?php
		if ($this->acl->checkPermissions("modules","shop","updateengine")) {
		?>
		<a href="<?=base_url()?>modules/shop/updateengine?confirm=1" class="button medium-button primary-button">
			<?=$this->lang->line("update")?>
		</a>
		<?php
		}
		?>		
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</div>