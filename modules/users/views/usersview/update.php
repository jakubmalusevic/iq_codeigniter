<form method="post" action="<?=base_url()?>users/users/<?=$updateown?"updateown":"update"?>/<?=$item->id?>" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
	<div class="modal-header">
		<?=$this->lang->line("edit_user")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label for="username"><?=$this->lang->line("username")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="username" name="username" value="<?=$item->username?>" class="full-width" required-field="true" validation="[not-empty]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="change_password"><?=$this->lang->line("change_password")?>?</label>
			</div>
			<div class="column-6">
				<input type="checkbox" id="change_password" name="change_password" value="1" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="new_password"><?=$this->lang->line("new_password")?></label>
			</div>
			<div class="column-6">
				<input type="password" id="new_password" name="new_password" class="full-width" required-field="true" validation="[not-empty]" validate-on-checked="change_password" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="full_name"><?=$this->lang->line("user_full_name")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="full_name" name="full_name" value="<?=$item->full_name?>" class="full-width" required-field="true" validation="[not-empty]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
		if ($this->acl->checkPermissions("users","roles","index")) {
		?>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="role_id"><?=$this->lang->line("role")?></label>
			</div>
			<div class="column-6">
				<select name="role_id" id="role_id" class="full-width" required-field="true" validation="[not-empty]">
					<option value=""><?=$this->lang->line("select_role")?></option>
					<?php
					for($r=0;$r<count($all_roles);$r++){
					?>
					<option value="<?=$all_roles[$r]->id?>" <?=$item->role_id==$all_roles[$r]->id?"selected=\"selected\"":""?>><?=$all_roles[$r]->name?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>		
		<?php
		} else {
		?>
		<input type="hidden" name="role_id" value="<?=$item->role_id?>" />
		<?php
		}
		?>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="email"><?=$this->lang->line("email")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="email" name="email" value="<?=$item->email?>" class="full-width" required-field="true" validation="[email]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="activated"><?=$this->lang->line("activated")?></label>
			</div>
			<div class="column-6">
				<?php
				if ($item->id!=$this->session->userdata("user_id")) {
				?>
				<input type="checkbox" id="activated" name="activated" value="1" <?=$item->activated==1?"checked=\"checked\"":""?> />
				<?php
				} else {
				?>
				<input type="checkbox" id="activated" name="activated" value="1" <?=$item->activated==1?"checked=\"checked\"":""?> disabled="disabled" tooltip-text="<?=$this->lang->line("prevent_deactivation_text")?>" />
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
		$this->event->register("UserUpdateFormRow",$item);
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