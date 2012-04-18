CREATE TABLE IF NOT EXISTS `engine4_slider_slides` (
  `slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `enable_slide` tinyint(4) NOT NULL DEFAULT '1',
  `link` text COLLATE utf8_bin NOT NULL,
  `title` text COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`slide_id`),
  KEY `order` (`order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('slider', 'Slider', 'Add Slides to a page.', '4.0.1', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES
('slider_admin_main_slides', 'slider', 'Slides', '', '{"route":"admin_default","module":"slider","controller":"settings", "action":"slides"}', 'slider_admin_main', '', 0, 1, 1),
('slider_admin_main_settings', 'slider', 'Settings', '', '{"route":"admin_default","module":"slider","controller":"settings", "action":"index"}', 'slider_admin_main', '', 0, 2, 1),
('core_admin_main_plugins_slider', 'slider', 'Slider', '', '{"route":"admin_default","module":"slider","controller":"settings"}', 'core_admin_main_plugins', '', 0, 999, 1)
