<form method="post" action="<?=base_url()?>modules/shop/buy/<?=$this->uri->segment(4)?>/<?=$this->uri->segment(5)?>" class="modal-wrapper column-6" validate-form="true" validation-error="<?=$this->lang->line("please_check_marked_fields")?>">
	<div class="modal-header">
		<?=$this->lang->line("buy_module")?>
	</div>	
	<table class="work-table" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<?php
				$columns=8;
				?>
				<th><?=$this->lang->line("icon")?></th>
				<th><?=$this->lang->line("name")?></th>
				<th><?=$this->lang->line("author")?></th>
				<th><?=$this->lang->line("version")?></th>
				<th><?=$this->lang->line("price")?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$total=0;
			$neeed_modules=0;
			for($i=0;$i<count($item->dependencies);$i++) {
				$check_module=$this->ModulesModel->checkInstalledModule($item->dependencies[$i]->name);
				if (!$check_module) $neeed_modules++;
			}			
			if ($neeed_modules>0) {
			?>
			<tr>
				<td colspan="5" class="related-modules-buy-header"><?=$this->lang->line("related_modules_which_should_be_installed")?></td>
			</tr>
			<?php
			for($i=0;$i<count($item->dependencies);$i++) {
			if (!$this->ModulesModel->checkInstalledModule($item->dependencies[$i]->name)) {
			if (isset($item->dependencies[$i]->author)) {
			$total+=$item->dependencies[$i]->price;
			?>
			<tr>
				<td class="align-center"><img src="<?=$item->dependencies[$i]->icon?>" /></td>
				<td class="align-center"><?=$item->dependencies[$i]->title?></td>
				<td class="align-center"><?=$item->dependencies[$i]->author?></td>
				<td class="align-center"><?=$item->dependencies[$i]->latest_version?></td>
				<td class="align-center">
					$<?=$item->dependencies[$i]->price?>
					<input type="hidden" name="related_modules[]" value="<?=$item->dependencies[$i]->id?>" />
					<input type="hidden" name="modules[]" value="<?=$item->dependencies[$i]->id?>" />
				</td>
			</tr>
			<?php
			} else {
			?>
			<tr>
				<td class="align-center"><img src="<?=$this->theme->getModuleIcon('empty')?>" /></td>
				<td class="align-center"><?=$item->dependencies[$i]->title?></td>
				<td class="align-center">-</td>
				<td class="align-center">-</td>
				<td class="align-center">$0.00</td>
			</tr>
			<?php				
			}
			}
			}
			}
			$total+=$item->price;
			?>		
			<tr>
				<td colspan="5" class="related-modules-buy-header"><?=$this->lang->line("module_to_buy")?></td>
			</tr>			
			<tr>
				<td class="align-center"><img src="<?=$item->icon?>" /></td>
				<td class="align-center"><?=$item->title?></td>
				<td class="align-center"><?=$item->author?></td>
				<td class="align-center"><?=$item->latest_version?></td>
				<td class="align-center">
					$<?=$item->price?>
					<input type="hidden" name="modules[]" value="<?=$item->id?>" />
				</td>
			</tr>	
			<tr>
				<td colspan="4" class="buy-module-total">
					<?=$this->lang->line("total")?>
				</td>
				<td class="buy-module-total-value">
					$<?=number_format($total,2,".","")?>
				</td>
			</tr>							
		</tbody>
	</table>	
	<div class="clearfix"></div>
	<div class="modal-footer">
		<?php
		if ($total>0) {
		?>
		<input type="submit" value="<?=$this->lang->line("purchase")?>" class="button medium-button primary-button" />
		<?php
		} else {
		?>
		<input type="submit" value="<?=$this->lang->line("install_for_free")?>" class="button medium-button primary-button" />
		<input type="hidden" name="_free_install" value="1" />
		<?php
		}
		?>
		<a href="#" class="button medium-button secondary-button close-modal-window">
			<?=$this->lang->line("close")?>
		</a>		
	</div>
</form>