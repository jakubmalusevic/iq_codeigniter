<script src="<?=base_url()?>modules/modules/assets/class.Modules.js" type="text/javascript"></script>
<script src="<?=base_url()?>modules/modules/assets/masonry.js" type="text/javascript"></script>
<link href="<?=base_url()?>modules/modules/assets/Modules.css" rel="stylesheet" type="text/css" />
<?php
if ($this->theme->direction=="rtl") {
?>
<link href="<?=base_url()?>modules/modules/assets/Modules.rtl.css" rel="stylesheet" type="text/css" />
<?php
}
?>
<?php
$get_request=explode("?",$_SERVER['REQUEST_URI']);
if (count($get_request)>1) $get_request="?".$get_request[1];
else $get_request="";
?>
<section class="content">
	<div class="content-inner">
		<div class="tabs-wrapper">
			<ul class="tabs-list" id="tabs-list">
				<?php
				if ($this->acl->checkPermissions("modules","listing","index")) {
				?>				
				<li>
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
				<li class="active">
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
			<form action="<?=base_url()?>modules/shop/index" method="get">
				<div class="column-6 modules-shop-search-field">
					<div class="inline-form-row">
						<input class="full-width modules-big-field" type="text" value="<?=isset($_GET['filter']['search_module'])?$_GET['filter']['search_module']:""?>" placeholder="<?=$this->lang->line("search_module")?>" name="filter[search_module]" />
					</div>
				</div>
				<div class="column-6 modules-shop-search-field">
					<div class="inline-form-row">
						<select class="full-width modules-big-field" name="filter[category_id]">
							<option value=""><?=$this->lang->line("select_category")?></option>
							<?php
							for($i=0;$i<count($categories);$i++) {
							?>
							<option value="<?=$categories[$i]->id?>" <?=isset($_GET['filter']['category_id'])?($_GET['filter']['category_id']==$categories[$i]->id?"selected=\"selected\"":""):""?>><?=$categories[$i]->title." (".$categories[$i]->modules_count.")"?></value>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="column-6 modules-shop-search-field">
					<div class="inline-form-row">
						<input class="full-width modules-big-field" type="text" value="" placeholder="<?=$this->lang->line("enter_tags")?>" tags-input="true" tags-field-name="filter[tags][]" tags-container="shop-search-tags" />
					</div>
					<div id="shop-search-tags">
					<?php
					if (isset($_GET['filter']['tags'])) {
						if (is_array($_GET['filter']['tags'])) {
							for ($i=0;$i<count($_GET['filter']['tags']);$i++) {
							?>
							<span class="single-tag"><?=$_GET['filter']['tags'][$i]?><i remove-tag="true" class="remove-tag-icon">&nbsp;</i><input type="hidden" value="<?=$_GET['filter']['tags'][$i]?>" name="filter[tags][]"></span>
							<?php
							}
						}
					}
					?>
					</div>
				</div>
				<div class="column-6 modules-shop-search-field">
					<div class="inline-form-row">
						<button class="button modules-big-button primary-button" name="apply_filters" type="submit">
							<i class="typcn typcn-zoom-outline"></i>
							<?=$this->lang->line("search")?>
						</button>
					</div>
				</div>
				<div class="clearfix"></div>				
			</form>
		</div>	
		<div class="content-body">
			<div class="content-action">
				<div class="content-action-inner" id="modules-container">
					<?php
					if (count($modules)==0) {
					?>
					<div class="shop-module-no-modules">
						<?=$this->lang->line("no_modules_found")?>
					</div>
					<?php
					}
					?>
					<?php
					for($i=0;$i<count($modules);$i++) {
					?>
					<div class="shop-module-wrapper column-4">
						<?php
						if ($this->acl->checkPermissions("modules","shop","view")) {
						?>
						<a href="<?=base_url()?>modules/shop/view/<?=$modules[$i]->id.$get_request?>" class="shop-module-box">
						<?php
						} else {
						?>
						<div class="shop-module-box">
						<?php
						}
						?>						
							<?php
							$rating=0;
							if ($modules[$i]->avg_rating>0) $rating=$modules[$i]->avg_rating;
							?>
							<div class="shop-module-box-rating">
								<div class="shop-module-box-rating-star">
									<div class="shop-module-box-rating-star-bg" style="width:<?=17*$rating/5?>px;"></div>
									<div class="shop-module-box-rating-star-image"></div>
								</div>
								<?=$modules[$i]->avg_rating!=""?number_format($modules[$i]->avg_rating,1,".",""):"0.0"?>
							</div>
							<h3 class="shop-module-header">
								<?php
								if ($modules[$i]->icon!="") {
								?>
								<img src="<?=$modules[$i]->icon?>" />
								<?php
								}
								?>
								<?=$modules[$i]->title?>
							</h3>
							<div class="shop-module-author"><?=$this->lang->line("by")?> <?=$modules[$i]->author?></div>
							<?php
							if (count($modules[$i]->images)>0) {
							?>
							<div class="shop-module-images-wrp">
								<img src="<?=$modules[$i]->images[0]->url?>" />
							</div>
							<?php
							}
							?>
							<div class="shop-module-price-container">
								<?php
								if ($modules[$i]->price==0) echo $this->lang->line("free_price");
								else echo "$".number_format($modules[$i]->price,2,".","");
								?>
								<?php
								if ($modules[$i]->installed_version!==false) {
								if ($modules[$i]->installed_version!=$modules[$i]->latest_version) {
								?>
								<br/>
								<span class="new-module-version"><?=$this->lang->line("new_version")?></span>
								<?php
								}
								}
								?>
							</div>
							<?=$modules[$i]->description?>
							<?php
							$tags=explode(";",$modules[$i]->tags);
							if (count($tags)>0) {
							?>
							<div class="shop-module-tags-container">
								<?php
								for($t=0;$t<count($tags);$t++) {
								?>
								<span class="shop-module-tag"><?=$tags[$t]?></span>
								<?php
								}
								?>
							</div>
							<?php
							}
							?>
							<div class="shop-module-version-info">
								<?=$this->lang->line("current_version")?>
								<?=$modules[$i]->latest_version?>
								<?php
								if ($modules[$i]->installed_version!==false){
								?>
								/
								<span class="<?=$modules[$i]->installed_version!=$modules[$i]->latest_version?"shop-module-bad-version":"shop-module-good-version"?>">
									<?=$this->lang->line("installed_version")?>
									<?=$modules[$i]->installed_version?>								
								</span>
								<?php
								}
								?>
							</div>
						<?php
						if ($this->acl->checkPermissions("modules","shop","view")) {
						?>
							<div class="shop-module-box-cover"></div>
						</a>
						<?php
						} else {
						?>
						</div>
						<?php
						}
						?>	
					</div>
					<?php
					}
					?>
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
<script>
$(window).bind("load",function(){
	$("#modules-container").masonry({
	  itemSelector:".shop-module-wrapper"
	});
});
</script>