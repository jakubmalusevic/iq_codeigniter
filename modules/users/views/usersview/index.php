<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<?php
				if ($this->acl->checkPermissions("users","users","index")) {
				?>
				<li class="active">
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
				<li>
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
			if ($this->acl->checkPermissions("users","users","create")) {
			?>
			<a href="<?=base_url()?>users/users/create" class="button big-button primary-button modal-window">
				<i class="typcn typcn-plus"></i>
				<?=$this->lang->line("create_new_user")?>
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
								$columns=6;
								?>
								<th sort-column="users.username" <?=@$_GET['sort-column']==""?"sort-direction=\"asc\"":""?>><?=$this->lang->line("username")?></th>
								<th sort-column="users.full_name" class="s-static-hide"><?=$this->lang->line("user_full_name")?></th>
								<th sort-column="users.email" class="s-static-hide"><?=$this->lang->line("email")?></th>
								<th sort-column="role_name"><?=$this->lang->line("role")?></th>
								<th sort-column="users.activated"><?=$this->lang->line("activated")?></th>
								<th sort-column="users.last_activity" class="xs-static-hide"><?=$this->lang->line("last_activity")?></th>
								<?php
								$this->event->register("UsersTableHeading",$columns);
								?>
								<?php
								if ($this->acl->checkPermissions("users","users","update") || $this->acl->checkPermissions("users","users","updateown") || $this->acl->checkPermissions("users","users","delete")){
								$columns++
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
								<td class="align-center"><?=$items[$i]->username?></td>
								<td class="align-center s-static-hide"><?=$items[$i]->full_name?></td>
								<td class="align-center s-static-hide"><?=$items[$i]->email?></td>
								<td class="align-center">
									<?php
									if ($this->acl->checkPermissions("users","roles","index") && $items[$i]->role_name!="") {
									?>								
									<a href="<?=base_url()?>users/roles/index?filter[role_name]=<?=$items[$i]->role_name?>&apply_filters=" class="work-table-link"><?=$items[$i]->role_name?></a>
									<?php
									} else {
									?>
									<?=$items[$i]->role_name!=""?$items[$i]->role_name:'<span class="red-line">'.$this->lang->line("deleted_role").'</span>'?>
									<?php
									}
									?>
								</td>
								<td class="align-center"><?=$items[$i]->activated==1?$this->lang->line("yes"):$this->lang->line("no")?></td>
								<td class="align-center xs-static-hide"><?=$items[$i]->last_activity!="0000-00-00 00:00:00"?date("d/m/Y H:i",strtotime($items[$i]->last_activity)):$this->lang->line("never")?></td>
								<?php
								$this->event->register("UsersTableRow",$items[$i],$i);
								?>	
								<?php
								if ($this->acl->checkPermissions("users","users","update") || $this->acl->checkPermissions("users","users","updateown") || $this->acl->checkPermissions("users","users","delete")){								
								?>							
								<td class="align-center">
									<?php
									if ($this->acl->checkPermissions("users","users","update")){
									?>
									<a href="<?=base_url()?>users/users/update/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("edit_user")?>" ><i class="typcn typcn-pencil"></i></a>
									<?php
									} elseif ($this->acl->checkPermissions("users","users","updateown") && $items[$i]->id==$this->session->userdata("user_id")) {
									?>									
									<a href="<?=base_url()?>users/users/updateown/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("edit_user")?>" ><i class="typcn typcn-pencil"></i></a>									
									<?php
									}
									?>
									<?php
									if ($this->acl->checkPermissions("users","users","delete") && $items[$i]->id!=1 && $items[$i]->id!=$this->session->userdata("user_id")) {
									?>
									<a href="<?=base_url()?>users/users/delete/<?=$items[$i]->id?>" class="table-action-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_user")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("delete_user")?>"><i class="typcn typcn-trash"></i></a>
									<?php
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