<script src="<?=base_url()?>modules/log/assets/class.Log.js" type="text/javascript"></script>
<link href="<?=base_url()?>modules/log/assets/Log.css" rel="stylesheet" type="text/css" />
<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<li class="active">
					<a href="#">
						<?=$this->lang->line("log")?>
						<?php
						$_section_description=$this->language->getSectionDescription("log","listing");
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
			if ($this->acl->checkPermissions("log","listing","clear")) {
			?>
			<a href="<?=base_url()?>log/listing/clear" class="button big-button primary-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_clear_system_log")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>">
				<i class="typcn typcn-backspace-outline"></i>
				<?=$this->lang->line("clear_system_log")?>
			</a>
			<?php
			}
			?>
			<?php
			if ($this->acl->checkPermissions("log","listing","batchdelete")) {
			?>
			<a href="javascript:log.deleteSelected();" class="button big-button primary-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_selected_log_records")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" id="batch-delete" style="display:none;">
				<i class="typcn typcn-trash"></i>
				<?=$this->lang->line("delete_selected")?>
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
								$columns=4;
								if ($this->acl->checkPermissions("log","listing","batchdelete")) {
								$columns++;
								?>
								<th>
									<?php
									if (count($items)>0) {
									?>
									<input type="checkbox" select-all="log-row" tooltip-text="<?=$this->lang->line("select_unselect_all")?>" />
									<?php
									}
									?>
								</th>
								<?php
								}
								?>
								<th sort-column="log.date_time" <?=@$_GET['sort-column']==""?"sort-direction=\"desc\"":""?>><?=$this->lang->line("date")?></th>
								<th sort-column="log.log_type"><?=$this->lang->line("log_type")?></th>
								<th sort-column="log.description"><?=$this->lang->line("description")?></th>
								<th sort-column="made_by_name" class="xs-static-hide"><?=$this->lang->line("action_made_by")?></th>
								<?php
								$this->event->register("LogTableHeading",$columns);
								?>									
								<?php
								if ($this->acl->checkPermissions("log","listing","delete")) {
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
								<?php
								if ($this->acl->checkPermissions("log","listing","batchdelete")) {
								?>
								<td class="align-center">
									<input type="checkbox" select-all-child="log-row" batch-handler="log-row" batch-related="batch-delete" name="ids[]" value="<?=$items[$i]->id?>" />
								</td>
								<?php
								}
								?>							
								<td class="align-center"><?=date("d/m/Y H:i",strtotime($items[$i]->date_time))?></td>
								<td class="align-center"><?=$this->lang->line("log_type_".$items[$i]->log_type)?></td>
								<td class="log-record-description">
									<?php
									$prepared_log_description=htmlspecialchars($items[$i]->description);
									if (mb_strlen($prepared_log_description)<=200) {
										echo nl2br($prepared_log_description);
									} else {
									?>
									<div class="log-description-preview">
										<?=nl2br(mb_substr($prepared_log_description,0,200)."...")?>
									</div>
									<div class="log-description-full">
										<?=nl2br($prepared_log_description)?>
									</div>
									<a href=#" class="button middle-button primary-button" log-show-more="true">
										<i class="typcn typcn-eye-outline"></i>
										<?=$this->lang->line("show_more")?>
									</a>
									<?php
									}
									?>
								</td>
								<td class="align-center xs-static-hide">
									<?php
									if ($this->acl->checkPermissions("users","users","index") && $items[$i]->made_by_name!="") {
									?>
									<a href="<?=base_url()?>users/users/index?filter[user_name]=&filter[user_full_name]=<?=urlencode($items[$i]->made_by_name)?>&filter[user_role]=&apply_filters=" class="work-table-link"><?=$items[$i]->made_by_name?></a>
									<?php
									} else {
									?>
									<?=$items[$i]->made_by_name!=""?$items[$i]->made_by_name:'<span class="red-line">'.$this->lang->line("deleted_user").'</span>'?>
									<?php
									}
									?>
								</td>
								<?php
								$this->event->register("LogTableRow",$items[$i],$i);
								?>									
								<?php
								if ($this->acl->checkPermissions("log","listing","delete")) {
								?>								
								<td class="align-center">							
									<a href="<?=base_url()?>log/listing/delete/<?=$items[$i]->id?>" class="popup-action table-action-button" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_delete_log_record")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>" tooltip-text="<?=$this->lang->line("delete_log_record")?>"><i class="typcn typcn-trash"></i></a>							
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