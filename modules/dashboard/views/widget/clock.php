<?php
$widget_id="clock_widget_".rand(0,200);
?>
<link href="<?=base_url()?>modules/dashboard/assets/ClockWidget.css" rel="stylesheet" type="text/css" />
<script src="<?=base_url()?>modules/dashboard/assets/class.ClockWidget.js" type="text/javascript"></script>
<div class="widget-clock-box" id="<?=$widget_id?>">
	<div class="widget-clock-date-row">
		<span day-container="true"><?=date("d")?></span>
		/
		<span month-container="true"><?=date("m")?></span>
		/
		<span year-container="true"><?=date("Y")?></span>
	</div>
	<div class="widget-clock-time-row">
		<span hour-container="true"><?=date("H")?></span>
		:
		<span minute-container="true"><?=date("i")?></span>
		:
		<span second-container="true"><?=date("s")?></span>
	</div>	
</div>
<script>
var _clock_widget_handler_<?=$widget_id?>=new ClockWidget("#<?=$widget_id?>");
</script>