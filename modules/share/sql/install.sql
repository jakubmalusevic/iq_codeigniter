CREATE TABLE IF NOT EXISTS `[table_prefix]share_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `buy_price` float NOT NULL,
  `sell_price` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `commission` text NOT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
