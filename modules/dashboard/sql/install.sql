CREATE TABLE IF NOT EXISTS `[table_prefix]dashboard_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `widget_name` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;