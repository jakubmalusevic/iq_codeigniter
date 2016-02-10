<div class="modal-wrapper column-12">
	<div class="modal-header">
		<?=$this->lang->line("image")?>
		<?=$image->current_number?>
		<?=$this->lang->line("of")?>
		<?=$image->total_images?>
	</div>	
	<div class="modal-content">
		<img src="<?=$image->current_image->url?>" class="full-module-image" />
		<div class="clearfix"></div>
	</div>	
	<div class="modal-footer">
		<?php
		if ($image->prev_image!=false && $this->acl->checkPermissions("modules","shop","viewimage")) {
		?>
		<a href="<?=base_url()?>modules/shop/viewimage/<?=$image->prev_image->id?>" class="button medium-button primary-button modal-window">
			<?=$this->lang->line("previous_image")?>	
		</a>
		<?php
		}
		?>
		<?php
		if ($image->next_image!=false && $this->acl->checkPermissions("modules","shop","viewimage")) {
		?>
		<a href="<?=base_url()?>modules/shop/viewimage/<?=$image->next_image->id?>" class="button medium-button primary-button modal-window">
			<?=$this->lang->line("next_image")?>	
		</a>
		<?php
		}
		?>		
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>	
	</div>
</form>