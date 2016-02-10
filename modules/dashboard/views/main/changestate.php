<div class="modal-wrapper column-4">
	<div class="modal-header">
		<?=$this->lang->line("open_close_widgets")?>
	</div>	
	<table class="work-table" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<?php
				$columns=2;
				?>
				<th><?=$this->lang->line("widget")?></th>	
				<th><?=$this->lang->line("open")?></th>
				<?php
				$this->event->register("OpenWidgetsTableHeading",$columns);
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$n=0;
			foreach($items as $item){
			?>
			<tr>
				<td class="align-center"><?=$item->title?></td>
				<td class="align-center">
					<input type="checkbox" value="<?=$item->name?>" <?=$item->state->state>0?"checked=\"checked\"":""?> change-widget-state="true" />
				</td>
				<?php
				$this->event->register("OpenWidgetsTableRow",$item,$n);
				?>	
			</tr>
			<?php
			$n++;
			}
			if (count($items)==0) {
			?>
			<tr>
				<td class="no-records-found-row" colspan="<?=$columns?>"><?=$this->lang->line("no_widgets_found")?></td>
			</tr>
			<?php
			}
			?>							
		</tbody>
	</table>	
	<div class="clearfix"></div>
	<div class="modal-footer">
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</div>