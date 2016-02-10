<script src="<?=base_url()?>modules/modules/assets/class.Modules.js" type="text/javascript"></script>
<link href="<?=base_url()?>modules/modules/assets/Modules.css" rel="stylesheet" type="text/css" />
<?php
if ($this->theme->direction=="rtl") {
?>
<link href="<?=base_url()?>modules/modules/assets/Modules.rtl.css" rel="stylesheet" type="text/css" />
<?php
}
?>
<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<?php
				if ($this->acl->checkPermissions("modules","listing","index")) {
				?>				
				<li class="active">
					<a href="<?=base_url()?>modules/listing/index">
						<?=$this->lang->line("modules")?>
						<?php
						$_section_description=$this->language->getSectionDescription("modules","listing");
						if ($_section_description!="") {
						?>
						<i class="typcn typcn-info-large" tooltip-text="<?=$_section_description?>"></i>
						<?php
						}
						?>
					</a>
				</li>
				<?php
				}
				?>
				<?php
				if ($this->acl->checkPermissions("modules","shop","index")) {
				?>				
				<li>
					<a href="<?=base_url()?>modules/shop/index">
						<i class="typcn typcn-shopping-cart shop-cart-tab-icon"></i>
						<?=$this->lang->line("shop")?>
						<?php
						$_section_description=$this->language->getSectionDescription("modules","shop");
						if ($_section_description!="") {
						?>
						<i class="typcn typcn-info-large" tooltip-text="<?=$_section_description?>"></i>
						<?php
						}
						?>
					</a>
				</li>
				<?php
				}
				?>				
			</ul>
			<script>
			var _tabs=new Tabs("#tabs-list").bindEvents();
			</script>
			<div class="clearfix"></div>
		</div>
		<div class="content-header">
			<div class="column-6">
				<?php
				if ($this->acl->checkPermissions("modules","listing","refresh")) {
				?>
				<a href="<?=base_url()?>modules/listing/refresh" class="button big-button primary-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_refresh_modules")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("refresh_button_help_text")?>">
					<i class="typcn typcn-arrow-sync"></i>
					<?=$this->lang->line("refersh_modules")?>
				</a>
				<?php
				}
				?>
				<?php
				if ($this->acl->checkPermissions("modules","listing","install")) {
				?>			
				<a href="<?=base_url()?>modules/listing/install" class="button big-button primary-button modal-window">
					<i class="typcn typcn-download"></i>
					<?=$this->lang->line("install_new_module")?>
				</a>
				<?php
				}
				?>
			</div>
			<div class="column-6">
				<div class="engine-version-control">
					<span>
						<?=$this->lang->line("current_engine_version")?>
						<strong>v.<?=$this->config->config['version']?></strong>
					</span>
					<?php
					if ($this->acl->checkPermissions("modules","shop","updateengine") && $versions_info->engine->latest_version!=$this->config->config['version']) {
					?>
					<a href="<?=base_url()?>modules/shop/updateengine" class="button big-button primary-button modal-window">
						<i class="typcn typcn-arrow-sync"></i>
						<?=$this->lang->line("update_engine_to")." v.".$versions_info->engine->latest_version?>
					</a>
					<?php
					}
					?>					
					<?php
					if ($this->acl->checkPermissions("modules","shop","rollbackengine") && $versions_info->engine->latest_version==$this->config->config['version'] && count($versions_info->engine->all_versions)>1) {
					?>
					<a href="<?=base_url()?>modules/shop/rollbackengine" class="button big-button primary-button modal-window">
						<i class="typcn typcn-arrow-sync"></i>
						<?=$this->lang->line("rollback_engine_version")?>
					</a>
					<?php
					}
					?>					
				</div>
			</div>
			<div class="clearfix"></div>
		</div>	
		<div class="content-body">
			<?php
			$this->sidebar->renderSidebar("left-sidebar");
			?>	
			<div class="content-action bordered-left-sidebar">
				<div class="content-action-inner">
					<div class="content-action-header xs-static-hide"></div>
					<div class="content-action-subheader">
						<?php
						if (@$_GET['sort-column']=="" || (@$_GET['sort-column']=="modules.order" && @$_GET['sort-direction']=="asc")) {
							if ($this->acl->checkPermissions("modules","listing","saveorder")) {
								echo $this->lang->line("drag_modules_to_arrange_them_in_navigation");
							}
						}
						?>
					</div>
					<table class="work-table" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<?php
								$columns=8;
								?>
								<th><?=$this->lang->line("icon")?></th>
								<th sort-column="modules.title"><?=$this->lang->line("name")?></th>
								<th sort-column="modules.author" class="s-static-hide"><?=$this->lang->line("author")?></th>
								<th sort-column="modules.version" class="s-static-hide" style="width:80px;"><?=$this->lang->line("version")?></th>
								<th sort-column="modules.description" class="s-static-hide"><?=$this->lang->line("description")?></th>
								<th sort-column="modules.state" class="s-static-hide"><?=$this->lang->line("state")?></th>
								<th sort-column="modules.updated"><?=$this->lang->line("updated")?></th>
								<th sort-column="modules.order" <?=@$_GET['sort-column']==""?"sort-direction=\"asc\"":""?>><?=$this->lang->line("order")?></th>
								<?php
								$this->event->register("ModulesTableHeading",$columns);
								?>										
								<?php
								if ($this->acl->checkPermissions("modules","listing","view") || ($this->acl->checkPermissions("modules","listing","delete") && $items[$i]->undeletable==0)) {
								$columns++;
								?>								
								<th></th>	
								<?php
								}
								?>							
							</tr>
						</thead>
						<?php
						$allow_draggable=false;
						if (@$_GET['sort-column']=="" || (@$_GET['sort-column']=="modules.order" && @$_GET['sort-direction']=="asc")) {
							if ($this->acl->checkPermissions("modules","listing","saveorder")) $allow_draggable=true;
						}
						?>
						<?php
						if ($allow_draggable) {
						?>
						<tbody sortable-wrapper="true" onStop="modules.rearrange(this);" start-order="<?=count($items)>0?$items[0]->order:10?>">
						<?php
						} else {
						?>
						<tbody>
						<?php
						}
						?>
							<?php
							for($i=0;$i<count($items);$i++) {
							?>
							<?php
							if ($allow_draggable) {
							?>
							<tr class="draggable-row">
							<?php
							} else {
							?>
							<tr>
							<?php
							}
							?>
								<td class="align-center"><img src="<?=$this->theme->getModuleIcon($items[$i]->name)?>" /></td>
								<td class="align-center"><?=$items[$i]->title?></td>
								<td class="s-static-hide align-center"><?=$items[$i]->author?></td>
								<td class="s-static-hide align-center">
									<?=$items[$i]->version?>
									<?php
									if (isset($versions_info->modules[$items[$i]->name]->version)) {
										if ($versions_info->modules[$items[$i]->name]->version!=$items[$i]->version) {
										?>
										<div class="module-available-version">
											<?php
											if ($this->acl->checkPermissions("modules","shop","view")) {
											?>
											<a href="<?=base_url()?>modules/shop/view/<?=$versions_info->modules[$items[$i]->name]->id?>" class="work-table-link"><?=$this->lang->line("available")." v.".$versions_info->modules[$items[$i]->name]->version?></a>
											<?php
											} else {
											?>
											<?=$this->lang->line("available")." v.".$versions_info->modules[$items[$i]->name]->version?>
											<?php
											}
											?>
										</div>
										<?php
										}
									}
									?>
								</td>
								<td class="s-static-hide"><?=$items[$i]->description?></td>
								<td class="s-static-hide align-center">
									<?php
									if ($this->acl->checkPermissions("modules","listing","changestate")) {
										if ($items[$i]->state==1) {
										?>
										<a href="<?=base_url()?>modules/listing/changestate/<?=$items[$i]->id?>?state=0" class="table-action-button green-icon" tooltip-text="<?=$this->lang->line("disable_module")?>" ><i class="typcn typcn-tick"></i></a>
										<?php
										} else {
										?>
										<a href="<?=base_url()?>modules/listing/changestate/<?=$items[$i]->id?>?state=1" class="table-action-button red-icon" tooltip-text="<?=$this->lang->line("enable_module")?>" ><i class="typcn typcn-delete"></i></a>
										<?php
										}
									?>
									<?php
									} else {
										echo $items[$i]->state==1?$this->lang->line("enabled"):$this->lang->line("disabled");
									}
									?>
								</td>
								<td class="align-center"><?=date("d/m/Y H:i",strtotime($items[$i]->updated))?></td>								
								<?php
								if ($allow_draggable) {
								?>
								<td class="align-center" order-cell="true" order-id="<?=$items[$i]->id?>"><?=$items[$i]->order?></td>
								<?php
								} else {
								?>
								<td class="align-center"><?=$items[$i]->order?></td>
								<?php
								}
								?>
								<?php
								$this->event->register("ModulesTableRow",$items[$i],$i);
								?>										
								<td class="align-center">
									<?php
									if ($this->acl->checkPermissions("modules","listing","view")) {
									?>
									<a href="<?=base_url()?>modules/listing/view/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("view_module_info")?>" ><i class="typcn typcn-eye"></i></a>
									<?php
									}
									?>									
									<?php
									if ($this->acl->checkPermissions("modules","listing","delete") && $items[$i]->undeletable==0) {
									?>
									<a href="<?=base_url()?>modules/listing/delete/<?=$items[$i]->id?>" class="popup-action table-action-button" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_module")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("delete_module")?>"><i class="typcn typcn-trash"></i></a>
									<?php
									}
									?>								
								</td>								
							</tr>
							<?php
							}
							if (count($items)==0) {
							?>
							<tr>
								<td class="no-records-found-row" colspan="<?=$columns?>"><?=$this->lang->line("no_records_found")?></td>
							</tr>
							<?php
							}
							?>							
						</tbody>
					</table>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>	
			<div class="content-footer">
				<?php
				$this->pagination->drawPagination();
				?>
			</div>						
		</div>		
	</div>
</section>