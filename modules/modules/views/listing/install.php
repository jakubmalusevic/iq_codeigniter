<form method="post" action="<?=base_url()?>modules/listing/install" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>" enctype="multipart/form-data">
	<div class="modal-header">
		<?=$this->lang->line("install_module")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label for="archive"><?=$this->lang->line("module_archive")?></label>
			</div>
			<div class="column-6">
				<input class="full-width" type="file" id="archive" name="archive" required-field="true" validation="[not-empty][extension:zip]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
		$this->event->register("ModuleInstallFormRow");
		?>
		<div class="form-error-handler" error-handler="true"></div>
	</div>	
	<div class="modal-footer">
		<input type="submit" name="install" value="<?=$this->lang->line("install")?>" class="button medium-button primary-button" />
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</form>