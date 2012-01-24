
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_radcodes', 'radcodes', 'Radcodes', '', '{"route":"admin_default","module":"radcodes","controller":"index"}', 'core_admin_main_plugins', '', 999),
('radcodes_admin_main_index', 'radcodes', 'Overview', '', '{"route":"admin_default","module":"radcodes","controller":"index"}', 'radcodes_admin_main', '', 1),
('radcodes_admin_main_settings', 'radcodes', 'Global Settings', '', '{"route":"admin_default","module":"radcodes","controller":"settings"}', 'radcodes_admin_main', '', 2);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('radcodes', 'Radcodes', 'This module is Radcodes Core Library, and is required by all SocialEngine Modules developed by Radcodes.', '4.0.3', 1, 'extra');



-- --------------------------------------------------------

--
-- Table structure for table `engine4_radcodes_locations`
--

CREATE TABLE `engine4_radcodes_locations` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  
  `parent_type` varchar(32) NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  
  `formatted_address` varchar(255) NULL,
   
  `street_address` varchar(128) NULL,
  `city` varchar(64) NULL,
  `state` varchar(64) NULL,
  `country` varchar(64) NULL,
  `zip` varchar(64) NULL,
  
  `lat` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `lng` decimal(10,6) NOT NULL DEFAULT '0.000000',
  
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,

  PRIMARY KEY (`location_id`),
  KEY `parent` (`parent_type`,`parent_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
