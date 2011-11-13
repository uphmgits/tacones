
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: my.sql 7244 2010-09-01 01:49:53Z john $
 * @author	   John
 */


-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_marketplace_categories`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `engine4_marketplace_categories`
--

INSERT INTO `engine4_marketplace_categories` (`category_id`, `user_id`, `category_name`) VALUES
(1, 1, 'Arts & Culture'),
(2, 1, 'Business'),
(3, 1, 'Entertainment'),
(5, 1, 'Family & Home'),
(6, 1, 'Health'),
(7, 1, 'Recreation'),
(8, 1, 'Personal'),
(9, 1, 'Shopping'),
(10, 1, 'Society'),
(11, 1, 'Sports'),
(12, 1, 'Technology'),
(13, 1, 'Other');

--
-- Структура таблицы `engine4_marketplace_fields_maps`
--
DROP TABLE IF EXISTS `engine4_marketplace_fields_maps`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `engine4_marketplace_fields_maps`
--

--
-- Table structure for table `engine4_marketplace_marketplaces`
--
--
-- Структура таблицы `engine4_marketplace_marketplaces`
--
DROP TABLE IF EXISTS `engine4_marketplace_marketplaces`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_marketplaces` (
  `marketplace_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `closed` tinyint(4) NOT NULL DEFAULT '0',
  `price` decimal(16, 2) NOT NULL DEFAULT '0',
  `business_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`marketplace_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------
--
-- Структура таблицы `engine4_marketplace_fields_meta`
--

DROP TABLE IF EXISTS `engine4_marketplace_fields_meta`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


--
-- Структура таблицы `engine4_marketplace_fields_options`
--
DROP TABLE IF EXISTS `engine4_marketplace_fields_options`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Структура таблицы `engine4_marketplace_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `field_3` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_4` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_5` enum('1','2','3','4') DEFAULT NULL,
  `field_6` enum('5','6','7','8','9') DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`),
  KEY `field_3` (`field_3`),
  KEY `field_4` (`field_4`),
  KEY `field_5` (`field_5`),
  KEY `field_6` (`field_6`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `engine4_marketplace_fields_search`
--

INSERT INTO `engine4_marketplace_fields_search` (`item_id`, `price`, `location`, `field_3`, `field_4`, `field_5`, `field_6`) VALUES
(3, NULL, NULL, NULL, NULL, '2', '7');

--
-- Структура таблицы `engine4_marketplace_fields_values`
--
DROP TABLE IF EXISTS `engine4_marketplace_fields_values`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `engine4_marketplace_fields_values`
--

INSERT INTO `engine4_marketplace_fields_values` (`item_id`, `field_id`, `index`, `value`) VALUES
(3, 5, 0, '2'),
(3, 6, 0, '7');
--
-- Table structure for table `engine4_marketplace_albums`
--

DROP TABLE IF EXISTS `engine4_marketplace_albums`;
CREATE TABLE `engine4_marketplace_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`album_id`),
  KEY `marketplace_id` (`marketplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_marketplace_photos`
--

DROP TABLE IF EXISTS `engine4_marketplace_photos`;
CREATE TABLE `engine4_marketplace_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned NOT NULL,
  `marketplace_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `marketplace_id` (`marketplace_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Table structure for table `engine4_marketplace_fields_meta`
--

DROP TABLE IF EXISTS `engine4_marketplace_fields_meta`;
CREATE TABLE `engine4_marketplace_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,
  /*`unit` varchar(32) COLLATE utf8_unicode_ci NOT NULL,*/

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


--
-- Table structure for table `engine4_marketplace_fields_search`
--

DROP TABLE IF EXISTS `engine4_marketplace_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_marketplace_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double NULL,
  `location` varchar(255) NULL,
  PRIMARY KEY  (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `engine4_marketplace_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `marketplace_id` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1',
  `summ` varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_marketplace', 'marketplace', 'Marketplace', '', '{"route":"marketplace_browse"}', 'core_main', '', 4),
('core_sitemap_marketplace', 'marketplace', 'Marketplace', '', '{"route":"marketplace_browse"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_marketplace', 'marketplace', 'Marketplace', '', '{"route":"admin_default","module":"marketplace","controller":"settings"}', 'core_admin_main_plugins', '', 999),

('marketplace_admin_main_manage', 'marketplace', 'View Marketplace', '', '{"route":"admin_default","module":"marketplace","controller":"manage"}', 'marketplace_admin_main', '', 1),
('marketplace_admin_main_settings', 'marketplace', 'Global Settings', '', '{"route":"admin_default","module":"marketplace","controller":"settings"}', 'marketplace_admin_main', '', 2),
('marketplace_admin_main_level', 'marketplace', 'Member Level Settings', '', '{"route":"admin_default","module":"marketplace","controller":"level"}', 'marketplace_admin_main', '', 3),
('marketplace_admin_main_fields', 'marketplace', 'Marketplace Questions', '', '{"route":"admin_default","module":"marketplace","controller":"fields"}', 'marketplace_admin_main', '', 4),
('marketplace_admin_main_categories', 'marketplace', 'Categories', '', '{"route":"admin_default","module":"marketplace","controller":"settings","action":"categories"}', 'marketplace_admin_main', '', 5),
('marketplace_admin_main_reports', 'marketplace', 'Reports', '', '{"route":"admin_default","module":"marketplace","controller":"manage","action":"reports"}', 'marketplace_admin_main', '', 6);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('marketplace', 'Marketplace', 'Marketplace', '4.2.0', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('marketplace.currency', '$');


-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('marketplace_new', 'marketplace', '{item:$subject} posted a new marketplace listing:', 1, 5, 1, 3, 1, 1),
('comment_marketplace', 'marketplace', '{item:$subject} commented on {item:$owner}''s {item:$object:marketplace listing}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('comment_marketplace', 'marketplace', '{item:$subject} has commented on your {item:$object:marketplace listing}.', 0, ''),
('like_marketplace', 'marketplace', '{item:$subject} likes your {item:$object:marketplace listing}.', 0, ''),
('commented_marketplace', 'marketplace', '{item:$subject} has commented on a {item:$object:marketplace listing} you commented on.', 0, ''),
('liked_marketplace', 'marketplace', '{item:$subject} has commented on a {item:$object:marketplace listing} you liked.', 0, '')
;


-- --------------------------------------------------------


INSERT INTO `engine4_marketplace_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0,	0,	1,	9999),
(0,	0,	2,	9999);
INSERT INTO `engine4_marketplace_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1,	'select',	'Size',	'',	'',	0,	1,	0,	999,	'{\"show\":\"1\"}',	NULL,	NULL,	'',	''),
(2,	'radio',	'Color',	'',	'',	0,	1,	0,	999,	'{\"show\":\"1\"}',	NULL,	NULL,	'',	'');
INSERT INTO `engine4_marketplace_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1,	1,	'L',	999),
(2,	1,	'XL',	999),
(3,	1,	'XXL',	999),
(4,	2,	'Red',	999),
(5,	2,	'Green',999),
(6,	2,	'Blue',	999);

INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('marketplace_transaction_to_owner',	'marketplace',	'Your retrive payment for {item:$object:$label}.',	0,	'');
INSERT INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_marketplace_transaction_to_owner',	'marketplace',	'[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('marketplace_transaction_to_buyer',	'marketplace',	'Your payment for {item:$object:$label} has been successfull.',	0,	'');
INSERT INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_marketplace_transaction_to_buyer',	'marketplace',	'[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');



-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'css' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'css' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'css' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'style' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'css' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'style' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'marketplace' as `type`,
    'photo' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');