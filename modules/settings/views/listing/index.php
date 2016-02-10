<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<li class="active">
					<a href="#">
						<?=$this->lang->line("settings")?>
						<?php
						$_section_description=$this->language->getSectionDescription("settings","listing");
						if ($_section_description!="") {
						?>
						<i class="typcn typcn-info-large" tooltip-text="<?=$_section_description?>"></i>
						<?php
						}
						?>
					</a>
				</li>
			</ul>
			<script>
			var _tabs=new Tabs("#tabs-list").bindEvents();
			</script>
			<div class="clearfix"></div>
		</div>
		<div class="content-header">
			<?php
			if ($this->acl->checkPermissions("settings","listing","reset")) {
			?>
			<a href="<?=base_url()?>settings/listing/reset" class="button big-button primary-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_reset_global_settings")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("refresh_button_help_text")?>">
				<i class="typcn typcn-arrow-sync"></i>
				<?=$this->lang->line("reset_global_settings_to_default")?>
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
								<th sort-column="section_title" <?=@$_GET['sort-column']==""?"sort-direction=\"asc\"":""?>><?=$this->lang->line("section")?></th>
								<th sort-column="title"><?=$this->lang->line("setting_name")?></th>
								<th sort-column="value"><?=$this->lang->line("setting_value")?></th>
								<?php
								$this->event->register("SettingsTableHeading",$columns);
								?>
								<?php
								if ($this->acl->checkPermissions("settings","listing","update")){
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
								<td class="align-center"><?=$items[$i]->section_title?></td>
								<td class="align-center"><?=$items[$i]->title?></td>
								<td class="align-center"><?=htmlspecialchars(mb_strlen($items[$i]->value)>200?mb_substr($items[$i]->value,0,200)."...":$items[$i]->value)?></td>
								<?php
								$this->event->register("SettingsTableRow",$items[$i],$i);
								?>	
								<?php
								if ($this->acl->checkPermissions("settings","listing","update")){
								?>							
								<td class="align-center">
									<a href="<?=base_url()?>settings/listing/update/<?=$items[$i]->id?>" class="table-action-button modal-window" tooltip-text="<?=$this->lang->line("edit_setting")?>" ><i class="typcn typcn-pencil"></i></a>		
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