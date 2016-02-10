<form method="post" action="<?=base_url()?>settings/listing/update/<?=$item->id?>" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
	<div class="modal-header">
		<?=$this->lang->line("edit_setting_modal_title")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("section")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->section_title?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("setting_name")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->title?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>		
		<div class="inline-form-row">
			<div class="column-6">
				<label for="setting_value"><?=$this->lang->line("setting_value")?></label>
			</div>
			<div class="column-6">
				<?php
				if (count($item->options)==0) {
				?>
				<textarea name="setting_value" id="setting_value" class="full-width"><?=$item->value?></textarea>
				<?php
				} else {
				?>
				<select name="setting_value" id="setting_value" class="full-width" required-field="true" validation="[not-empty]">
					<option value="">- <?=$this->lang->line("select_value")?> -</option>
					<?php
					foreach($item->options as $value=>$label){
					?>
					<option value="<?=$value?>" <?=$value==$item->value?"selected=\"selected\"":""?>><?=$label?></option>
					<?php
					}
					?>
				</select>
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
		$this->event->register("SettingUpdateFormRow",$item);
		?>		
		<div class="form-error-handler" error-handler="true"></div>
	</div>	
	<div class="modal-footer">
		<input type="submit" value="<?=$this->lang->line("update")?>" class="button medium-button primary-button" />
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</form>