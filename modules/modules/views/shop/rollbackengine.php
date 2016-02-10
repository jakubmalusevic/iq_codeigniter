<form method="post" action="<?=base_url()?>modules/shop/rollbackengine" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
	<div class="modal-header">
		<?=$this->lang->line("rollback_engine_version")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label for="rollback-version"><?=$this->lang->line("rollback_to_version")?></label>
			</div>
			<div class="column-6">
				<select name="version_id" class="full-width" id="rollback-version" required-field="true" validation="[not-empty]">
					<option value=""><?=$this->lang->line("select_version")?></option>
					<?php
					for($i=0;$i<count($versions);$i++){
					?>
					<option value="<?=$versions[$i]->version?>"><?=$versions[$i]->version?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="form-error-handler" error-handler="true"></div>
	</div>	
	<div class="modal-footer">
		<input type="submit" value="<?=$this->lang->line("rollback")?>" class="button medium-button primary-button" />
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</form>