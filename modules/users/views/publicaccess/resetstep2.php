<link href="<?=base_url()?>modules/users/assets/Users.css" rel="stylesheet" type="text/css" />
<script src="<?=base_url()?>modules/users/assets/class.Users.js" type="text/javascript"></script>
<?php
if ($this->theme->direction=="rtl") {
?>
<link href="<?=base_url()?>modules/users/assets/Users.rtl.css" rel="stylesheet" type="text/css" />
<?php
}
?>
<div class="engine-logo-wrapper">
	<img src="<?=base_url()?>assets/images/engine-logo.png" />
</div>
<div class="modal-wrapper column-3 ajax-form-wrapper" id="login-reset-box">
	<?php
	if ($user_found) {
	?>
	<form id="reset-form" method="post" action="<?=base_url()?>users/publicaccess/newpassword" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>" autocomplete="off" ajax-form="true" callback="users.newpasswordFormSent(response);">
		<input type="hidden" name="reset_token" value="<?=$reset_token?>" />
		<div class="modal-header">
			<?=$this->lang->line("reset_password")?>
		</div>
		<div class="modal-content">	
			<div class="inline-form-row">
				<?=$this->lang->line("your_username_is")?>:
				<strong><?=$user_name?></strong>
			</div>		
			<div class="inline-form-row" id="reset-from-state">
				<?=$this->lang->line("please_put_your_new_password_and_confirm_it")?>
			</div>
			<div id="reset-form-control">
				<div class="inline-form-row">
					<div class="column-6">
						<label for="password"><?=$this->lang->line("password")?></label>
					</div>
					<div class="column-6">
						<input type="password" id="password" name="password" class="full-width" required-field="true" validation="[not-empty]" autocomplete="off" />
					</div>
					<div class="clearfix"></div>
				</div>	
				<div class="inline-form-row">
					<div class="column-6">
						<label for="confirm_password"><?=$this->lang->line("confirm_password")?></label>
					</div>
					<div class="column-6">
						<input type="password" id="confirm_password" name="confirm_password" class="full-width" required-field="true" validation="[match-field:password]" autocomplete="off" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-error-handler" error-handler="true"></div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" value="<?=$this->lang->line("reset")?>" class="button medium-button primary-button" />
			<a href="<?=base_url()?>users/publicaccess/login" class="users-forgot-passwrod-link work-table-link"><?=$this->lang->line("back_to_login")?></a>
		</div>
	</form>
	<?php
	} else {
	?>
	<div class="modal-header">
		<?=$this->lang->line("reset_password")?>
	</div>	
	<div class="modal-content">	
		<div class="form-error-handler" style="display:block;"><?=$this->lang->line("there_was_no_request_to_reset_password_by_this_link")?></div>
	</div>	
	<div class="modal-footer">
		<a href="<?=base_url()?>users/publicaccess/login" class="users-forgot-passwrod-link work-table-link"><?=$this->lang->line("back_to_login")?></a>
	</div>	
	<?php
	}
	?>
</div>
<script>
$.magnificPopup.open({
	items:{
		src:"#login-reset-box"
	},
	type:"inline",
	mainClass:'anim-mfp-slide-bottom',
	removalDelay:300,
	closeOnBgClick:false,
	modal:true
}, 0);
</script>