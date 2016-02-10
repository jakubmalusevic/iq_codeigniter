<!DOCTYPE HTML>
<html>
<head>
	<?php
	$this->event->register("BeforeHeadHTML");
	?>
	<meta charset="utf-8" />
	<title><?=$this->theme->main_title.($page_title!=""?$this->theme->page_title_delimiter.$page_title:"")?></title>
	<meta name="viewport" id="system_viewport_handler" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link href="<?=base_url()?>assets/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="<?=base_url()?>assets/css/icons.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/themes/<?=$this->theme->color_scheme?>/theme.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/themes/<?=$this->theme->color_scheme?>/ui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" type="text/css" />
	<?php
	if ($this->theme->direction=="rtl") {
	?>
	<link href="<?=base_url()?>assets/css/style.rtl.css" rel="stylesheet" type="text/css" />
	<link href="<?=base_url()?>assets/css/responsive.rtl.css" rel="stylesheet" type="text/css" />
	<?php
	}
	?>
	<script src="<?=base_url()?>js/language?module=<?=CI::$APP->router->fetch_module()?>" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/jquery.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/jquery.form.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.Popup.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.Navigation.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.Tabs.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.Hideable.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.FormValidator.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.VisualHelpers.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/magnific-popup/jquery.magnific-popup.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>assets/js/class.Events.js" type="text/javascript"></script>
	<script>
	var base_url="<?=base_url()?>";
	</script>
	<?php
	$this->event->register("AfterHeadHTML");
	?>	
</head>
<body>
<?php
$this->event->register("BeforeNavigationHTML");
$this->language->drawLanguageNavigator();
$this->event->register("AfterNavigationHTML");
if (!$this->acl->isGuest()) {
	$this->navigation->drawNavigation();
	if ($this->notifications->checkNotifications()) {
	?>
	<div class="notifications-content-wrapper">
		<div class="content-inner">
		<?php
		$this->event->register("BeforeNotificationsHTML");
		$this->notifications->drawNotifications();
		$this->event->register("AfterNotificationsHTML");
		?>
		</div>
	</div>
	<?php
	}
}
$this->event->register("BeforeModuleHTML");
?>