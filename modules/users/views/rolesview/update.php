<form method="post" action="<?=base_url()?>users/roles/update/<?=$item->id?>" class="modal-wrapper column-8" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?><br/><?=$this->lang->line("make_sure_at_least_one_primary_action_should_be_allowed")?>">
	<div class="modal-header">
		<?=$this->lang->line("edit_role")?>
	</div>	
	<div class="modal-content">
		<div class="inline-form-row">
			<div class="column-4">
				<label for="name"><?=$this->lang->line("name")?></label>
			</div>
			<div class="column-4">
				<input type="text" id="name" name="name" class="full-width" value="<?=$item->name?>" required-field="true" validation="[not-empty]" />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-4">
				<label for="full_access"><?=$this->lang->line("full_access")?></label>
			</div>
			<div class="column-4">
				<input type="checkbox" id="full_access" name="full_access" value="1" <?=$item->full_access==1?"checked=\"checked\"":""?> />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="inline-form-row">
			<div class="column-4">
				<label><?=$this->lang->line("navigation_and_permissions")?></label>
			</div>
			<div class="column-8">
				<?php
				$permissions=array();
				$permissions=unserialize($item->permissions);				
				for ($i=0;$i<count($modules);$i++){
				$sections=array();
				$sections=@unserialize($modules[$i]->sections);
				?>
				<div class="permission-module-block">
					<div class="permission-box">
						<div class="column-6">
							<a href="#" class="plus-minus-toggler" related-to="hideable-sections-block-<?=$i?>"></a>
							<strong>
								<?php
								$module_title=$this->language->getModuleTitle($modules[$i]->name);
								if ($module_title=="") $module_title=$modules[$i]->title;
								echo $module_title;
								?>							
							</strong>
						</div>
						<div class="column-6">
							<input type="checkbox" id="show_in_navigation_<?=$i?>" name="permissions[<?=$modules[$i]->name?>][show_in_navigation]" value="1" <?=isset($permissions[$modules[$i]->name]['show_in_navigation'])?($permissions[$modules[$i]->name]['show_in_navigation']==1?"checked=\"checked\"":""):""?> />
							<label for="show_in_navigation_<?=$i?>"><?=$this->lang->line("show_in_navigation")?></label>		
						</div>		
						<div class="clearfix"></div>				
					</div>
					<div class="permission-sections-block" id="hideable-sections-block-<?=$i?>" style="display:none;">
						<?php
						for($s=0;$s<count($sections);$s++){
						?>
						<div class="permission-box<?=$s==count($sections)-1?" last-permission-box":""?>">
							<div class="column-6">
								<a href="#" class="plus-minus-toggler" related-to="hideable-actions-block-<?=$i?>-<?=$s?>"></a>
								<strong>
									<?php
									$section_title=$this->language->getSectionTitle($modules[$i]->name,$sections[$s]->name);
									if ($section_title=="") $section_title=$sections[$s]->title;
									echo $section_title;
									?>										
								</strong>
							</div>
							<div class="column-6">
								<input type="checkbox" id="allow_all_actions_<?=$i?>_<?=$s?>" value="1" onchange="roles.checkRelatedActions(this);" />
								<label for="allow_all_actions_<?=$i?>_<?=$s?>"><?=$this->lang->line("allow_all_actions")?></label>		
							</div>
							<div class="clearfix"></div>							
						</div>	
						<div class="permission-actions-block" id="hideable-actions-block-<?=$i?>-<?=$s?>" style="display:none;">
							<?php
							for($a=0;$a<count($sections[$s]->actions);$a++){
							?>
							<div class="permission-box<?=$a==count($sections[$s]->actions)-1?" last-permission-box":""?>">
								<div class="column-6">
									<?php
									$action_title=$this->language->getActionTitle($modules[$i]->name,$sections[$s]->name,$sections[$s]->actions[$a]->name);
									if ($action_title=="") $action_title=$sections[$s]->actions[$a]->title;
									echo $action_title.($a==0?" <span class=\"roles-primary-action-subtitle\">(".$this->lang->line("primary_action").")</span>":"");
									?>
								</div>
								<div class="column-6">
									<input type="checkbox" id="allow_action_<?=$i?>_<?=$s?>_<?=$a?>" name="permissions[<?=$modules[$i]->name?>][<?=$sections[$s]->name?>][]" value="<?=$sections[$s]->actions[$a]->name?>" onchange="roles.checkAllActions();" <?=($this->acl->checkPermissions($modules[$i]->name,$sections[$s]->name,$sections[$s]->actions[$a]->name,$item->id))?"checked=\"checked\"":""?> <?=$a==0?"required-field=\"true\" validation=\"[roles-check-primary-action:primary-action]\"":""?>/>
									<label for="allow_action_<?=$i?>_<?=$s?>_<?=$a?>"><?=$this->lang->line("allow_action")?></label>		
								</div>
								<div class="clearfix"></div>							
							</div>															
							<?php
							}
							?>
						</div>
						<?php
						}
						?>
					</div>
				</div>
				<?php
				}
				?>			
			</div>
			<div class="clearfix"></div>
		</div>	
		<?php
		$this->event->register("RoleUpdateFormRow",$item);
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
<script>
roles.checkAllActions();
</script>