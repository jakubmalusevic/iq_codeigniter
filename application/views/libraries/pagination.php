<div class="pagination-wrapepr">
	<span><?=$this->lang->line("displaying")?> <?=$parent->getItemsNumbers()?> <?=$this->lang->line("of")?> <?=$parent->total_count?></span>
	<div class="pagination-action-wrapper">
		<a href="<?=$parent->firstPageURL()?>">
			<i class="typcn typcn-media-rewind"></i>
		</a>
		<a href="<?=$parent->prevPageURL()?>">
			<i class="typcn typcn-media-play-reverse"></i>
		</a>
		<span><?=$this->lang->line("page")?></span>
		<input type="text" value="<?=$page2display?>" default-value="<?=$page2display?>" page-field="true" />
		<a href="<?=$parent->nextPageURL()?>">
			<i class="typcn typcn-media-play"></i>
		</a>							
		<a href="<?=$parent->lastPageURL()?>">
			<i class="typcn typcn-media-fast-forward"></i>
		</a>							
		<a href="#" onclick="location.reload();return false;">
			<i class="typcn typcn-arrow-sync"></i>
		</a>
	</div>
</div>