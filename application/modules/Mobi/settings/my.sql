
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: my.sql 9378 2011-10-13 22:50:30Z john $
 * @author     Charlotte
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('mobi', 'Mobi', 'Mobile Layout', '4.1.8', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('mobi_footer', 'standard', 'Mobile Footer Menu'),
('mobi_main', 'standard', 'Mobile Main Menu'),
('mobi_profile', 'standard', 'Mobile Profile Options Menu'),
('mobi_browse', 'standard', 'Mobile Browse Page Menu')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_footer_mobile', 'mobi', 'Mobile Site', 'Mobi_Plugin_Menus', '', 'core_footer', '', 4),

('mobi_footer_mobile', 'mobi', 'Mobile Site', 'Mobi_Plugin_Menus', '', 'mobi_footer', '', 1),
('mobi_footer_auth', 'mobi', 'Auth', 'Mobi_Plugin_Menus', '', 'mobi_footer', '', 2),
('mobi_footer_signup', 'mobi', 'Sign Up', 'Mobi_Plugin_Menus', '', 'mobi_footer', '', 3),

('mobi_main_home', 'mobi', 'Home', 'Mobi_Plugin_Menus', '', 'mobi_main', '', 1),
('mobi_main_profile', 'mobi', 'Profile', 'Mobi_Plugin_Menus', '', 'mobi_main', '', 2),
('mobi_main_messages', 'mobi', 'Inbox', 'Mobi_Plugin_Menus', '', 'mobi_main', '', 3),
('mobi_main_browse', 'mobi', 'Browse', 'Mobi_Plugin_Menus', '', 'mobi_main', '', 4),

('mobi_profile_message', 'mobi', 'Send Message', 'Mobi_Plugin_Menus', '', 'mobi_profile', '', 1),
('mobi_profile_friend', 'mobi', 'Friends', 'Mobi_Plugin_Menus', '', 'mobi_profile', '', 2),

('mobi_browse_members', 'user', 'Members', '', '{"route":"user_general","action":"browse"}', 'mobi_browse', '', 1);