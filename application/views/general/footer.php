<?php
$this->event->register("AfterModuleHTML");
if ($this->lang->hasUntranslatedWords()) {
?>
<style>
.floating-not-translated-wrapper{
	position:fixed;
	bottom:35px;
	padding:0px 5px;
	font-size:12px;
	line-height:20px;
	left:0px;
	right:0px;
	text-align:center;
}
.floating-not-translated-message{
	display:inline-block;
	zoom:1;
	*display:inline;
	vertical-align:top;
	padding:5px;
	background:#E24D4D;
	color:#FFF;
	border-radius:5px;
	opacity:0.5;
	transition:0.3s;
}
.floating-not-translated-message:hover{
	opacity:1;
}
body{
	padding-bottom:35px;
}
</style>
<div class="floating-not-translated-wrapper">
	<div class="floating-not-translated-message"><?=$this->lang->line("words_marked_with_asteriks_not_translated")?></div>
</div>
<?php
}
?>
	<script>
	var _hideable=new Hideable().bindEvents();	
	visualHelpers.init();
	_events.initDraggable();
	</script>
</body>
</html>