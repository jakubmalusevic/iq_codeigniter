<script src="<?=base_url()?>modules/users/assets/class.Roles.js" type="text/javascript"></script>
<link href="<?=base_url()?>modules/users/assets/Roles.css" rel="stylesheet" type="text/css" />
<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<?php
				if ($this->acl->checkPermissions("users","users","index")) {
				?>
				<li>
					<a href="<?=base_url()?>users/users/index">
						<?=$this->lang->line("users")?>
						<?php
						$_section_description=$this->language->getSectionDescription("users","users");
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
				if ($this->acl->checkPermissions("users","roles","index")) {
				?>
				<li class="active">
					<a href="<?=base_url()?>users/roles/index">
						<?=$this->lang->line("roles")?>
						<?php
						$_section_description=$this->language->getSectionDescription("users","roles");
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
			<?php
			if ($this->acl->checkPermissions("users","roles","create")) {
			?>
			<a href="<?=base_url()?>users/roles/create" class="button big-button primary-button modal-window">
				<i class="typcn typcn-plus"></i>
				<?=$this->lang->line("create_new_role")?>
			</a>
			<?php
			}
			?>
		</div>	
		<div class="content-body">
			<?php
			$this->sidebar->renderSidebar("left-sidebar");
			?>	
			<div class="content-action bordered-left-sidebar">
				<div class="content-action-inner">
					<div class="content-action-header xs-static-hide"></div>
					<div class="content-action-subheader">
					</div>
					<table class="work-table" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<?php
								$columns=3;
								?>							
								<th sort-column="roles.name" <?=@$_GET['sort-column']==""?"sort-direction=\"asc\"":""?>><?=$this->lang->line("role_name")?></th>
								<th sort-column="roles.full_access"><?=$this->lang->line("full_access")?></th>
								<th sort-column="count_of_users"><?=$this->lang->line("number_of_users")?></th>
								<?php
								$this->event->register("RolesTableHeading",$columns);
								?>		
								<?php
								if ($this->acl->checkPermissions("users","roles","update") || $this->acl->checkPermissions("users","roles","delete")) {
								$columns++;
								?>						
								<th></th>	
								<?php
								}
								?>							
							</tr>
						</thead>
						<tbody>
							<?php
							for($i=0;$i<count($items);$i++) {
							?>
							<tr>
								<td class="align-center"><?=$items[$i]->name?></td>
								<td class="align-center"><?=$items[$i]->full_access==1?$this->lang->line("yes"):$this->lang->line("no")?></td>
								<td class="align-center">
									<?php
									if ($this->acl->checkPermissions("users","users","index")) {
									?>
									<a href="<?=base_url()?>users/users/index?filter[user_role]=<?=$items[$i]->id?>&apply_filters=" class="work-table-link"><?=$items[$i]->count_of_users?></a>
									<?php
									} else {
									?>
									<?=$items[$i]->count_of_users?>
									<?php
									}
									?>
								</td>
								<?php
								$this->event->register("RolesTableRow",$items[$i],$i);
								?>		
								<?php
								if ($this->acl->checkPermissions("users","roles","update") || $this->acl->checkPermissions("users","roles","delete")) {
								?>
								<td class="align-center">
									<?php
									if ($this->acl->checkPermissions("users","roles","update")) {
									?>
									<a href="<?=base_url()?>users/roles/update/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("edit_role")?>" ><i class="typcn typcn-pencil"></i></a>
									<?php
									}
									?>									
									<?php
									if ($this->acl->checkPermissions("users","roles","delete") && $items[$i]->id!=1) {
									if ($items[$i]->count_of_users==0) {
									?>
									<a href="<?=base_url()?>users/roles/delete/<?=$items[$i]->id?>" class="table-action-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_role")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("delete_role")?>"><i class="typcn typcn-trash"></i></a>
									<?php
									} else {
									?>
									<a href="#" class="table-action-button popup-action" popup-type="warning" popup-message="<?=$this->lang->line("you_cannot_delete_role")?>" tooltip-text="<?=$this->lang->line("delete_role")?>"><i class="typcn typcn-trash"></i></a>
									<?php
									}
									}
									?>								
								</td>	
								<?php
								}
								?>							
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