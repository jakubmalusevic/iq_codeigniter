<div class="language-wrapper">
	<?=$this->lang->line("_lang_name")?>
	<ul class="language-navigator">
		<?php
		foreach($items as $lang=>$lang_name){
		if ($lang!=$current_language) {
		?>
		<li select-language="<?=$lang?>">
			<?=$lang_name?>
		</li>
		<?php
		}
		}
		?>
	</ul>
</div>