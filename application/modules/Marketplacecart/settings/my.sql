DROP TABLE IF EXISTS `engine4_marketplace_cart`;
CREATE TABLE `engine4_marketplace_cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `marketplace_id` int(11) NOT NULL,
  `count` int(11) DEFAULT '1',
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;