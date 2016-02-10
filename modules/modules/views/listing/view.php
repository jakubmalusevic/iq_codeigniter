<div class="modal-wrapper column-4">
	<div class="modal-header">
		<?=$this->lang->line("module_info")?>
	</div>
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("name")?></label>
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
				<label><?=$this->lang->line("system_name")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->name?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("icon")?></label>
			</div>
			<div class="column-6">
				<img src="<?=$this->theme->getModuleIcon($item->name)?>" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("description")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->description?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("state")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->state==1?$this->lang->line("enabled"):$this->lang->line("disabled")?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>		
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("version")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->version?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("author")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->author?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("updated")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=date("d/m/Y H:i",strtotime($item->updated))?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("order")?></label>
			</div>
			<div class="column-6">
				<label>
					<strong><?=$item->order?></strong>
				</label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("sections_and_actions")?></label>
			</div>
			<div class="column-6">
				<?php
				$sections=unserialize($item->sections);
				for($s=0;$s<count($sections);$s++) {
				?>
				<div>
					<label>
						<strong><?=$sections[$s]->title?></strong>
					</label>
				</div>
				<?php
				for($a=0;$a<count($sections[$s]->actions);$a++) {
				?>
				<div class="left-offset-10">
					<label>
						- <?=$sections[$s]->actions[$a]->title?>
					</label>
				</div>				
				<?php
				}
				?>
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-6">
				<label><?=$this->lang->line("depended_on_modules")?></label>
			</div>
			<div class="column-6">
				<?php
				for($i=0;$i<count($item->depended_on);$i++) {
				?>
				<div>
					<label>
						<?php
						if ($this->ModulesModel->checkInstalledModule($item->depended_on[$i]->required_module_name)) {
						?>
						<i class="typcn typcn-tick green-icon"></i>
						<?php
						} else {
						?>
						<i class="typcn typcn-delete red-icon"></i>
						<?php
						}
						?>
						&nbsp;
						<strong><?=isset($item->depended_on[$i]->title)?$item->depended_on[$i]->title:$item->depended_on[$i]->required_module_name?></strong>
						<?php
						if (!$this->ModulesModel->checkInstalledModule($item->depended_on[$i]->required_module_name)) {
						?>
						&nbsp;
						<span class="gray-text">(<?=$this->lang->line("not_installed")?>)</span>
						<?php
						}
						?>						
					</label>
				</div>				
				<?php
				}
				if (count($item->depended_on)==0) {
				?>
				<div>
					<label>
						<strong><?=$this->lang->line("no_dependencies")?></strong>
					</label>
				</div>				
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		</div>	
		<?php
		$this->event->register("ModuleViewBoxRow",$item);
		?>			
	</div>
	<div class="modal-footer">
		<?php
		if ($this->acl->checkPermissions("modules","listing","changestate")) {
			if ($item->state==0) {
		?>
		<a href="<?=base_url()?>modules/listing/changestate/<?=$item->id?>?state=1" class="button medium-button primary-button">
			<?=$this->lang->line("enable_module_button")?>
		</a>		
		<?php
			} else {
		?>
		<a href="<?=base_url()?>modules/listing/changestate/<?=$item->id?>?state=0" class="button medium-button delete-button">
			<?=$this->lang->line("disable_module_button")?>
		</a>		
		<?php
			}
		}
		?>
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>
	</div>	
</div>