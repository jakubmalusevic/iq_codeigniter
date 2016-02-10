<div class="column-3 vertical-hideable sidebar-<?=$sidebar['position']?><?=!isset($_GET['apply_filters']) && $sidebar['is_filter']?" xs-vertical-hideable-hidden":""?>" id="<?=$name?>">
	<div class="filter-header vertical-hideable-handler" data-related-to="<?=$name?>">
		<div class="hideable-box">
			<?=$sidebar['title']?>
		</div>
	</div>
	<div class="xs-hideable-box">
		<div class="filter-subheader xs-static-hide"></div>
		<div class="filter-body">			 
			<?php
			if ($sidebar['is_filter']) {
			?>
			<form class="filter-form hideable-box" method="<?=$sidebar['filter_method']?>" action="<?=$sidebar['filter_action']?>">
			<?php
			} else {
			?>
			<div class="filter-form hideable-box">
			<?php
			}
			?>
				<?php
				if (count($sidebar['elements'])>0) {
				foreach($sidebar['elements'] as $element) {
				?>
				<div class="inline-form-row">
					<?php
					if ($element['type']=="text") {
					?>
					<input type="text" name="filter[<?=$element['name']?>]" class="full-width" placeholder="<?=$element['placeholder']?>" value="<?=isset($_GET['filter'][$element['name']])?$_GET['filter'][$element['name']]:$element['default_value']?>" />
					<?php
					} elseif ($element['type']=="textarea") {
					?>
					<textarea name="filter[<?=$element['name']?>]" class="full-width" placeholder="<?=$element['placeholder']?>"><?=isset($_GET['filter'][$element['name']])?$_GET['filter'][$element['name']]:$element['default_value']?></textarea>
					<?php	
					} elseif ($element['type']=="datepicker") {
					?>
					<input type="text" name="filter[<?=$element['name']?>]" class="full-width datepicker-field" placeholder="<?=$element['placeholder']?>" value="<?=isset($_GET['filter'][$element['name']])?$_GET['filter'][$element['name']]:$element['default_value']?>" />
					<?php
					} elseif ($element['type']=="checkbox") {
					?>
					<input type="checkbox" id="<?=$element['name']?>" name="filter[<?=$element['name']?>]" value="1" <?=$element['default_value']=="1" || $element['default_value']==1 || isset($_GET['filter'][$element['name']])?"checked=\"checked\"":""?> />
					<label for="<?=$element['name']?>"><?=$element['placeholder']?></label>
					<?php
					} elseif ($element['type']=="radio") {
					if (count($element['options'])>0) {
					foreach($element['options'] as $o=>$option){
					?>
					<div class="inline-form-row">
						<input type="radio" id="<?=$element['name']?>" name="filter[<?=$element['name']?>]" value="<?=$option['value']?>" <?=$element['default_value']==$option['value'] || @$_GET['filter'][$element['name']]==$option['value']?"checked=\"checked\"":""?> />
						<label for="<?=$element['name']?>_<?=$o?>"><?=$option['label']?></label>
					</div>
					<?php
					}
					}
					} elseif ($element['type']=="select") {
					?>
					<select name="filter[<?=$element['name']?>]" class="full-width">
						<?php
						if (count($element['options'])>0) {
						foreach($element['options'] as $o=>$option){
						?>
						<option value="<?=$option['value']?>" <?=$element['default_value']==$option['value'] || @$_GET['filter'][$element['name']]==$option['value']?"selected=\"selected\"":""?>><?=$option['label']?></option>
						<?php
						}
						}
						?>					
					</select>
					<?php
					} else {
						echo $element['placeholder'];
					}
					?>
				</div>					
				<?php
				}
				}
				?>
				<?php
				if ($sidebar['filter_event']!="") {
					$filter_array=array();
					if (isset($_GET['filter'])) $filter_array=$_GET['filter'];
					$this->event->register($sidebar['filter_event'],$filter_array);
				}
				?>
				<?php
				if ($sidebar['submit_button']!="" || $sidebar['reset_button']!="") {
				?>
				<div class="inline-form-row form-submit-row">
					<?php
					if ($sidebar['submit_button']!="") {
					?>
					<button type="submit" class="button medium-button primary-button" name="apply_filters">
						<?=$sidebar['submit_button']?>
					</button>
					<?php
					}
					?>
					<?php
					if ($sidebar['reset_button']!="") {
					?>					
					<a href="<?=$sidebar['filter_action']?>" class="button medium-button secondary-button">
						<?=$sidebar['reset_button']?>
					</a>
					<?php
					}
					?>
				</div>
				<?php
				}
				?>
			<?php
			if ($sidebar['is_filter']) {
			?>				
			</form>
			<?php
			} else {
			?>
			</div>
			<?php
			}
			?>			
		</div>
	</div>
</div>