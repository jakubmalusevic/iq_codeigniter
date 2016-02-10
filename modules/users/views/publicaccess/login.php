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
	<form id="login-form" method="post" action="<?=base_url()?>users/publicaccess/login" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>" autocomplete="off">
		<div class="modal-header">
			<?=$this->lang->line("login")?>
		</div>
		<div class="modal-content">
			<?php
			$this->notifications->drawNotifications();
			?>		
			<div class="inline-form-row">
				<div class="column-6">
					<label for="username"><?=$this->lang->line("username")?></label>
				</div>
				<div class="column-6">
					<input type="text" id="username" name="username" class="full-width" required-field="true" validation="[not-empty]" autocomplete="off" />
				</div>
				<div class="clearfix"></div>
			</div>	
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
					<label for="remember"><?=$this->lang->line("remember_me")?></label>
				</div>
				<div class="column-6">
					<input type="checkbox" id="remember" name="remember" value="1" />
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-error-handler" error-handler="true"></div>
		</div>
		<div class="modal-footer">
			<input type="submit" value="<?=$this->lang->line("login")?>" class="button medium-button primary-button" />
			<a href="#" class="users-forgot-passwrod-link work-table-link" onclick="users.switchView('reset');return false;"><?=$this->lang->line("forgot_password")?></a>
		</div>
	</form>
	<form id="reset-form" method="post" action="<?=base_url()?>users/publicaccess/reset" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>" autocomplete="off" style="display:none;" ajax-form="true" callback="users.resetFormSent(response);">
		<div class="modal-header">
			<?=$this->lang->line("reset_password")?>
		</div>
		<div class="modal-content">	
			<div class="inline-form-row" id="reset-from-state">
				<?=$this->lang->line("please_put_your_email_to_start_process_of_resetting_password")?>
			</div>
			<div id="reset-form-control">
				<div class="inline-form-row">
					<div class="column-6">
						<label for="email"><?=$this->lang->line("email")?></label>
					</div>
					<div class="column-6">
						<input type="text" id="email" name="email" class="full-width" required-field="true" validation="[email]" autocomplete="off" />
					</div>
					<div class="clearfix"></div>
				</div>	
				<div class="form-error-handler" error-handler="true"></div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="submit" value="<?=$this->lang->line("submit")?>" class="button medium-button primary-button" />
			<a href="#" class="users-forgot-passwrod-link work-table-link" onclick="users.switchView('login');return false;"><?=$this->lang->line("back_to_login")?></a>
		</div>
	</form>
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