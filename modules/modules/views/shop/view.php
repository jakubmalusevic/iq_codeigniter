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
				<div class="content-action-inner">
					<div class="detailed-module-container">
						<h1 class="detailed-module-container-header">
							<?php
							if ($module->icon!="") {
							?>
							<img src="<?=$module->icon?>" />
							<?php
							}
							?>	
							<?=$module->title?>						
						</h1>
						<div class="shop-module-author"><?=$this->lang->line("by")?> <?=$module->author?></div>
						<div class="column-6">
							<div class="rating-container">
								<div class="stars-wrapper">
									<?php
									$rating=$module->avg_rating!=""?$module->avg_rating:0;
									?>
									<?php
									for($i=1;$i<=5;$i++){
									?>
									<div class="star-box">
										<div class="star-bg" <?=$rating>$i?"style=\"width:100%;\"":($rating>$i-1 && $rating<=$i?"style=\"width:".((1-$i+$rating)*100)."%;\"":"")?>></div>
										<div class="star-cover"></div>
									</div>
									<?php
									}
									?>
									<div class="clearfix"></div>
								</div>	
								<span class="current-rating-number">		
									/
									<?=number_format($rating,1,".","")?>
								</span>
							</div>
							<div class="detailed-shop-module-price-container">
								<?php
								if ($module->price==0) echo $this->lang->line("free_price");
								else echo "$".number_format($module->price,2,".","");
								?>
							</div>	
							<?php
							if (($module->update && $this->acl->checkPermissions("modules","shop","update")) || ($module->buy && $this->acl->checkPermissions("modules","shop","buy")) || ($module->rollback && $this->acl->checkPermissions("modules","shop","rollback"))) {
							?>
							<div class="shop-module-action-button">						
								<?php
								if ($module->update && $this->acl->checkPermissions("modules","shop","update")) {
								?>
									<a href="<?=base_url()?>modules/shop/update/<?=$module->id?>/<?=$module->name?>" class="button big-button primary-button popup-action" popup-type="confirmation" popup-message="<?=$this->lang->line("you_really_want_to_update_module")?>" popup-buttons="confirm:<?=$this->lang->line("yes")?>,close:<?=$this->lang->line("cancel")?>">
										<i class="typcn typcn-arrow-sync"></i>
										<?=$this->lang->line("update_module_to")." v.".$module->latest_version?>
									</a>								
								<?php
								}
								?>
								<?php
								if ($module->buy && $this->acl->checkPermissions("modules","shop","buy")) {
								$total=0;
								for($i=0;$i<count($module->dependencies);$i++) {
									if (!$this->ModulesModel->checkInstalledModule($module->dependencies[$i]->name)) {
										$total+=$module->dependencies[$i]->price;
									}
								}		
								$total+=$module->price;								
								?>
									<a href="<?=base_url()?>modules/shop/buy/<?=$module->id?>/<?=$module->name?>" class="button big-button primary-button modal-window">
										<i class="typcn typcn-shopping-cart"></i>
										<?=$total>0?$this->lang->line("buy_module"):$this->lang->line("install_for_free")?>
									</a>								
								<?php
								}
								?>
								<?php
								if ($module->rollback && $this->acl->checkPermissions("modules","shop","rollback")) {
								?>
									<a href="<?=base_url()?>modules/shop/rollback/<?=$module->id?>/<?=$module->name?>" class="button big-button primary-button modal-window">
										<i class="typcn typcn-arrow-back-outline"></i>
										<?=$this->lang->line("rollback_module_version")?>
									</a>								
								<?php
								}
								?>								
							</div>
							<?php
							}
							?>
							<?php
							if (count($module->images)>0) {
							?>
							<div class="moudle-images-container">
							<?php
							for ($i=0;$i<count($module->images);$i++) {
							?>
							<?php
							if ($this->acl->checkPermissions("modules","shop","viewimage")) {
							?>
							<a href="<?=base_url()?>modules/shop/viewimage/<?=$module->images[$i]->id?>" class="image-thumbnail-box modal-window">
							<?php
							} else {
							?>
							<div class="image-thumbnail-box">
							<?php
							}
							?>
								<span class="image-thumbnail-internal-box align-center">
									<img src="<?=$module->images[$i]->url?>" />
								</span>
							<?php
							if ($this->acl->checkPermissions("modules","shop","viewimage")) {
							?>						
							</a>
							<?php
							} else {
							?>
							</div>
							<?php
							}
							?>						
							<?php
							}
							?>
								<div class="clearfix"></div>
							</div>
							<?php
							}
							?>
							<div class="shop-module-full-decription">
								<?=$module->description?>
							</div>
							<div class="shop-module-full-decription">
								<h3 class="shop-module-header"><?=$this->lang->line("latest_version_info")." (v.".$module->latest_version.")"?></h3>
								<?=$module->versions[0]->version_info?>
							</div>
						</div>
						<div class="column-6 module-detailed-info-column">
							<h3 class="shop-module-header"><?=$this->lang->line("module_info")?></h3>
							<br/>
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("category")?></label>
								</div>
								<div class="column-6">
									<strong><?=$module->category_title?></strong>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("tags")?></label>
								</div>
								<div class="column-6">
									<?php
									$tags=explode(";",$module->tags);
									if (count($tags)==0) echo "<strong>-</strong>";
									else {
										foreach($tags as $tag) {
										?>
										<span class="shop-module-tag"><?=$tag?></span>
										<?php
										}
									}
									?>
								</div>
								<div class="clearfix"></div>
							</div>	
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("in_shop_from")?></label>
								</div>
								<div class="column-6">
									<strong><?=date("d/m/Y",strtotime($module->uploaded))?></strong>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("latest_version")?></label>
								</div>
								<div class="column-6">
									<strong>v.<?=$module->latest_version?></strong>
								</div>
								<div class="clearfix"></div>
							</div>													
							<?php
							if ($module->installed_version!==false) {
							?>
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("installed_version")?></label>
								</div>
								<div class="column-6">
									<strong class="<?=$module->installed_version!=$module->latest_version?"shop-module-bad-version":"shop-module-good-version"?>">v.<?=$module->installed_version?></strong>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php
							}
							?>
							<div class="inline-form-row">
								<div class="column-6">
									<label><?=$this->lang->line("depended_on_modules")?></label>
								</div>
								<div class="column-6">
									<?php
									if (count($module->dependencies)==0) echo "<strong>-</strong>";
									else {
										foreach($module->dependencies as $depended_on_module) {
											$check_module=$this->ModulesModel->checkInstalledModule($depended_on_module->name);
											if ($check_module===false) {
											?>
											<div>
												<label>
													<i class="typcn typcn-delete red-icon"></i>
													&nbsp;
													<strong><?=$depended_on_module->title?></strong>
													(<?=$this->lang->line("module_not_instaled")?>)
												</label>
											</div>
											<?php
											} else {
											?>
											<div>
												<label>
													<i class="typcn typcn-tick green-icon"></i>
													&nbsp;
													<strong><?=$depended_on_module->title?></strong>
												</label>
											</div>
											<?php							
											}
										}
									}
									?>					
								</div>
								<div class="clearfix"></div>
							</div>	
							<?php
							if (count($module->reviews)>0) {
							?>
							<br/>
							<h3 class="shop-module-header"><?=$this->lang->line("reviews")." (".$module->count_reviews.")"?></h3>
							<br/>	
							<?php
							for($i=0;$i<count($module->reviews);$i++){
							?>
							<div class="inline-form-row review-box">
								<div class="column-12">
									<strong><?=$module->reviews[$i]->name?></strong>
									<?=$this->lang->line("at")?> <?=date("d/m/Y H:i",strtotime($module->reviews[$i]->added))?>						
								</div>
								<div class="column-12 module-review-stars-container">
									<?php
									$rating=$module->reviews[$i]->rating!=""?$module->reviews[$i]->rating:0;
									?>
									<?php
									for($s=1;$s<=5;$s++){
									?>
									<div class="small-star-box">
										<div class="small-star-bg" <?=$rating>$s?"style=\"width:100%;\"":($rating>$s-1 && $rating<=$s?"style=\"width:".((1-$s+$rating)*100)."%;\"":"")?>></div>
										<div class="small-star-cover"></div>
									</div>
									<?php
									}
									?>
									&nbsp;
									<span class="start-number"><?=number_format($rating,1,".","")?></span>					
								</div>
								<div class="column-12 review-text">
									<?=nl2br($module->reviews[$i]->comment)?>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php
							}
							?>	
							<?php
							}
							?>
							<?php
							if ($module->allow_comment && $this->acl->checkPermissions("modules","shop","createreview")) {
							?>
							<br/>
							<h3 class="shop-module-header"><?=$this->lang->line("leave_your_review")?></h3>
							<br/>	
							<form method="post" action="<?=base_url()?>modules/shop/createreview" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">		
								<input type="hidden" name="module_id" value="<?=$module->id?>" />
								<div class="inline-form-row">
									<div class="column-6">
										<label for="create-review-name"><?=$this->lang->line("your_name")?></label>
									</div>
									<div class="column-6">
										<input class="full-width" type="text" id="create-review-name" name="name" required-field="true" validation="[not-empty]" />
									</div>
									<div class="clearfix"></div>
								</div>				
								<div class="inline-form-row">
									<div class="column-6">
										<label><?=$this->lang->line("rate_this_module")?></label>
									</div>
									<div class="column-6">
										<div class="stars-wrapper" rating-handler="true">
											<input type="hidden" name="rating" value="0" />
											<?php
											for($i=1;$i<=5;$i++){
											?>
											<div class="small-star-box">
												<div class="small-star-bg"></div>
												<div class="small-star-cover"></div>
											</div>
											<?php
											}
											?>						
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="inline-form-row">
									<div class="column-12">
										<label for="create-review-comment"><?=$this->lang->line("review")?></label>
									</div>
									<div class="column-12">
										<textarea name="comment" id="create-review-comment" class="full-width" required-field="true" validation="[not-empty]"></textarea>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="form-error-handler" error-handler="true"></div>
								<div class="inline-form-row form-submit-row">
									<input type="submit" value="<?=$this->lang->line("submit_review")?>" class="button medium-button primary-button" />
								</div>
							</form>							
							<?php
							}
							?>																								
						</div>	
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>	
			<div class="content-footer">
			</div>						
		</div>		
	</div>
</section>