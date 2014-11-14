DROP TABLE IF EXISTS `#__jmsvm_colors`;
CREATE TABLE IF NOT EXISTS `#__jmsvm_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color_title` varchar(100) NOT NULL,
  `color_icon` varchar(50) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;

INSERT INTO `#__jmsvm_colors` (`id`, `color_title`, `color_icon`, `ordering`, `published`) VALUES
(2, 'red', 'red.png', 0, 1),
(3, 'Pink', 'pink.png', 0, 1),
(4, 'green', 'green.png', 0, 1),
(5, 'Purple', 'purple.png', 0, 1);

DROP TABLE IF EXISTS `#__jmsvm_product_colors`;
CREATE TABLE IF NOT EXISTS `#__jmsvm_product_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `color_imgs` varchar(400) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;