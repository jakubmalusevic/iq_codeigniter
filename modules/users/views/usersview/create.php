<form method="post" action="<?=base_url()?>users/users/create" class="modal-wrapper column-4" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
	<div class="modal-header">
		<?=$this->lang->line("create_user")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label for="username"><?=$this->lang->line("username")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="username" name="username" class="full-width" required-field="true" validation="[not-empty]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="password"><?=$this->lang->line("password")?></label>
			</div>
			<div class="column-6">
				<input type="password" id="password" name="password" class="full-width" required-field="true" validation="[not-empty]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="full_name"><?=$this->lang->line("user_full_name")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="full_name" name="full_name" class="full-width" required-field="true" validation="[not-empty]" />
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
					<option value="<?=$all_roles[$r]->id?>"><?=$all_roles[$r]->name?></option>
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
		<input type="hidden" name="role_id" value="<?=$this->session->userdata("user_role")?>" />
		<?php
		}
		?>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="email"><?=$this->lang->line("email")?></label>
			</div>
			<div class="column-6">
				<input type="text" id="email" name="email" class="full-width" required-field="true" validation="[email]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label for="activated"><?=$this->lang->line("activated")?></label>
			</div>
			<div class="column-6">
				<input type="checkbox" id="activated" name="activated" value="1" />
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
		$this->event->register("UserCreateFormRow");
		?>
		<div class="form-error-handler" error-handler="true"></div>
	</div>	
	<div class="modal-footer">
		<input type="submit" value="<?=$this->lang->line("create")?>" class="button medium-button primary-button" />
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</form>