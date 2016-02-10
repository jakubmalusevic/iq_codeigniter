<?php
if (!$exclude_wrapper) {
?>
<header>
	<div class="content-inner">
		<nav class="navigation-wrapper">
<?php
}
?>
			<div class="navigation-handler" id="navigation-handler">
				<i class="typcn typcn-th-menu"></i>
			</div>
			<ul class="navigation" id="primary-navigation">
				<?php
				for($i=0;$i<count($items);$i++) {
				?>
				<li>
					<?php
					if ($i<count($items)-1) {
					?>
					<div class="navigation-border"></div>
					<?php
					}
					?>
					<a href="<?=base_url().$items[$i]->module."/".$items[$i]->controller."/".$items[$i]->action?>" <?=$items[$i]->is_active?"class=\"active\"":""?>>
						<span class="navigation-icon-box">
							<img src="<?=$items[$i]->icon?>" class="navigation-icon-hide-on-hover" />
							<img src="<?=$items[$i]->icon_hovered?>" class="navigation-icon-display-on-hover" />
						</span>
						<span class="navigation-item-title"><?=$items[$i]->title?></span>
					</a>
				</li>
				<?php
				}
				?>														
			</ul>
			<div class="clearfix"></div>
<?php
if (!$exclude_wrapper) {
?>			
		</nav>
		<div class="clearfix"></div>
	</div>
</header>
<script>
var _navigation=new Navigation("#primary-navigation").adjustSize().setNavigationHandler("#navigation-handler").bindEvents();
</script>
<?php
}
?>