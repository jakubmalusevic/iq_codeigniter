<script src="<?=base_url()?>modules/dashboard/assets/class.Dashboard.js" type="text/javascript"></script>
<link href="<?=base_url()?>modules/dashboard/assets/Dashboard.css" rel="stylesheet" type="text/css" />
<?php
if ($this->theme->direction=="rtl") {
?>
<link href="<?=base_url()?>modules/dashboard/assets/Dashboard.rtl.css" rel="stylesheet" type="text/css" />
<?php
}
?>
<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<li class="active">
					<a href="#">
						<?=$this->lang->line("dashboard")?>
						<?php
						$_section_description=$this->language->getSectionDescription("dashboard","main");
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
			if ($this->acl->checkPermissions("dashboard","main","changestate")) {
			?>
			<a href="<?=base_url()?>dashboard/main/changestate" class="button big-button primary-button modal-window">
				<i class="typcn typcn-th-menu"></i>
				<?=$this->lang->line("open_close_widgets")?>
			</a>
			<?php
			}
			?>				
		</div>	
		<div class="content-body">
			<div class="content-action widgets-container-wrapper">
				<div class="content-action-inner">
					<div class="content-action-subheader">
					<?php
					if ($this->acl->checkPermissions("dashboard","main","rearrange")) {
					?>						
					<?=$this->lang->line("drag_widgets_to_arrange_them")?>
					<?php
					}
					?>
					</div>
					<?php
					if ($this->acl->checkPermissions("dashboard","main","rearrange")) {
					?>					
					<div class="widgets-container sortable-widgets-container" id="widgets-container">
					<?php
					} else {
					?>					
					<div class="widgets-container" id="widgets-container">
					<?php					
					}
					?>
						<?php
						foreach($items as $item){
						?>
						<div class="single-widget-box widget-box-<?=$item->size?>" widget-name="<?=$item->name?>" widget-order="<?=$item->state->order?>" widget-state="<?=$item->state->state?>" <?=$item->state->state==0?"style=\"display:none;\"":""?>>
							<div class="widget-internal-box">
								<div class="widget-header">
									<h3><?=$item->title?></h3>
									<?php
									if ($this->acl->checkPermissions("dashboard","main","changestate")) {
									?>						
									<span class="widget-close-button">
										<i class="typcn typcn-delete" tooltip-text="<?=$this->lang->line("close")?>"></i>
									</span>
									<span class="widget-collapse-button<?=$item->state->state==2?" closed":""?>">
										<i class="typcn typcn-arrow-sorted-up" tooltip-text="<?=$this->lang->line("collapse")?>"></i>
										<i class="typcn typcn-arrow-sorted-down" tooltip-text="<?=$this->lang->line("expand")?>"></i>
									</span>
									<?php
									}
									?>									
								</div>
								<div class="widget-content" <?=$item->state->state==2?"style=\"display:none;\"":""?>>
									<?=$item->html?>
								</div>
							</div>
						</div>
						<?php
						}
						?>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>	
		</div>		
	</div>
</section>