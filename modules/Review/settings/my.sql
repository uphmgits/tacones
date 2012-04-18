
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_reviews`
--

DROP TABLE IF EXISTS `engine4_review_reviews`;
CREATE TABLE `engine4_review_reviews` (
  `review_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `body` longtext NOT NULL,
  `pros` text NOT NULL,
  `cons` text NOT NULL,
  `keywords` varchar(255) NOT NULL, 
  `rating`  tinyint(4) NOT NULL default '0',
  `owner_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) NOT NULL default '1',
  `recommend`  tinyint(1) NOT NULL default '0',
  `featured`  tinyint(1) NOT NULL default '0',
  `vote_count` int(11) NOT NULL default '0',
  `helpful_count` int(11) NOT NULL default '0',
  `helpfulness` int(11) NOT NULL default '0',
  
  PRIMARY KEY (`review_id`),
  KEY `owner_id` (`owner_id`),
  KEY `user_id` (`user_id`),
  KEY `recommend` (`recommend`),
  KEY `featured` (`featured`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_votes`
--

DROP TABLE IF EXISTS `engine4_review_votes`;
CREATE TABLE `engine4_review_votes` (
  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `helpful`  tinyint(1) NOT NULL default '0',
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `review_user` (`review_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_fields_maps`
--

DROP TABLE IF EXISTS `engine4_review_fields_maps`;
CREATE TABLE `engine4_review_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

--
-- Dumping data for table `engine4_review_fields_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_fields_meta`
--

DROP TABLE IF EXISTS `engine4_review_fields_meta`;
CREATE TABLE `engine4_review_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `show` tinyint(1) unsigned NOT NULL default '1',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;



-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_fields_options`
--

DROP TABLE IF EXISTS `engine4_review_fields_options`;
CREATE TABLE `engine4_review_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_fields_values`
--

DROP TABLE IF EXISTS `engine4_review_fields_values`;
CREATE TABLE `engine4_review_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_review_fields_search`
--

DROP TABLE IF EXISTS `engine4_review_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_review_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

DELETE FROM engine4_core_menus WHERE name LIKE 'review_%';

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('review_main', 'standard', 'Review Main Navigation Menu'),
('review_admin_main', 'standard', 'Review Admin Main Navigation Menu'),
('review_quick', 'standard', 'Review Quick Navigation Menu'),
('review_gutter', 'standard', 'Review Gutter Navigation Menu'),
('review_toplist', 'standard', 'Review Toplist Navigation Menu')
;

--
-- Dumping data for table `engine4_core_menuitems`
--
DELETE FROM `engine4_core_menuitems` WHERE module = 'review';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_review', 'review', 'Reviews', '', '{"route":"review_home"}', 'core_main', '', 4),
('core_sitemap_review', 'review', 'Reviews', '', '{"route":"review_home"}', 'core_sitemap', '', 4),

('core_admin_main_plugins_review', 'review', 'Reviews', '', '{"route":"admin_default","module":"review","controller":"settings"}', 'core_admin_main_plugins', '', 999),

('user_profile_review', 'review', 'Write a Review', 'Review_Plugin_Menus', '{"route":"review_general","action":"create","class":"buttonlink icon_review_new"}', 'user_profile', '', 800),

('review_main_browse', 'review', 'Browse Reviews', 'Review_Plugin_Menus::canViewReviews', '{"route":"review_general","action":"browse"}', 'review_main', '', 1),
('review_main_manage', 'review', 'My Reviews', 'Review_Plugin_Menus::canCreateReviews', '{"route":"review_general","action":"manage"}', 'review_main', '', 2),
('review_main_create', 'review', 'Post New Review', 'Review_Plugin_Menus::canCreateReviews', '{"route":"review_general","action":"create"}', 'review_main', '', 3),

('review_main_toplist', 'review', 'Top Members', '', '{"route":"review_extended","module":"review","controller":"list","action":"top-rated-members"}', 'review_main', '', 4),


('review_quick_create', 'review', 'Post New Review', 'Review_Plugin_Menus::canCreateReviews', '{"route":"review_general","action":"create","class":"buttonlink icon_review_new"}', 'review_quick', '', 1),

('review_gutter_list', 'review', 'View All Reviews', 'Review_Plugin_Menus', '{"route":"review_user","class":"buttonlink icon_review_viewall"}', 'review_gutter', '', 1),
('review_gutter_create', 'review', 'Write New Review', 'Review_Plugin_Menus', '{"route":"review_general","action":"create","class":"buttonlink icon_review_new"}', 'review_gutter', '', 2),
('review_gutter_edit', 'review', 'Edit This Review', 'Review_Plugin_Menus', '{"route":"review_specific","action":"edit","class":"buttonlink icon_review_edit"}', 'review_gutter', '', 3),
('review_gutter_delete', 'review', 'Delete This Review', 'Review_Plugin_Menus', '{"route":"review_specific","action":"delete","class":"buttonlink icon_review_delete"}', 'review_gutter', '', 4),

('review_toplist_toprated', 'review', 'Top Rated Members', '', '{"route":"review_extended","module":"review","controller":"list","action":"top-rated-members"}', 'review_toplist', '', 1),
('review_toplist_mostrecommended', 'review', 'Most Recommended Members', '', '{"route":"review_extended","module":"review","controller":"list","action":"most-recommended-members"}', 'review_toplist', '', 2),
('review_toplist_mostreviewed', 'review', 'Most Reviewed Members', '', '{"route":"review_extended","module":"review","controller":"list","action":"most-reviewed-members"}', 'review_toplist', '', 3),
('review_toplist_topreviewers', 'review', 'Top Reviewers', '', '{"route":"review_extended","module":"review","controller":"list","action":"top-reviewers"}', 'review_toplist', '', 4),

('review_admin_main_manage', 'review', 'View Reviews', '', '{"route":"admin_default","module":"review","controller":"manage"}', 'review_admin_main', '', 1),
('review_admin_main_settings', 'review', 'Global Settings', '', '{"route":"admin_default","module":"review","controller":"settings"}', 'review_admin_main', '', 2),
('review_admin_main_level', 'review', 'Member Level Settings', '', '{"route":"admin_default","module":"review","controller":"level"}', 'review_admin_main', '', 3),
('review_admin_main_fields', 'review', 'Review Questions', '', '{"route":"admin_default","module":"review","controller":"fields"}', 'review_admin_main', '', 4)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

DELETE FROM `engine4_core_modules` WHERE name = 'review';

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('review', 'Reviews', 'This plugin allow your social network to have a review system between your members. This can help your site to build up trust, enhance quality of connections across your network. Super great for service provider, professional, colleague networking sites. Beside posting general review, it also support additional pros / cons listing, as well as rating stars. Recommendation can also be made by members, and many more etc.', '4.1.1', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--
DELETE FROM `engine4_core_settings` WHERE name LIKE 'review.%';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('review.license','XXXX-XXXX-XXXX-XXXX'),
('review.perpage','20'),
('review.toplimit','10');


-- --------------------------------------------------------
DELETE FROM `engine4_activity_actiontypes` WHERE module = 'review';

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('review_new', 'review', '{item:$subject} posted a new review:', 1, 5, 1, 3, 1, 1),
('comment_review', 'review', '{item:$subject} commented on {item:$owner}''s {item:$object:review}: {body:$body}', 1, 1, 1, 1, 1, 0);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
DELETE FROM `engine4_activity_notificationtypes` WHERE module = 'review';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('has_posted_review', 'review', '{item:$subject} posted a {item:$object:review} about you.', 0, ''),
('comment_review', 'review', '{item:$subject} has commented on your {item:$object:review}.', 0, ''),
('like_review', 'review', '{item:$subject} likes your {item:$object:review}.', 0, ''),
('commented_review', 'review', '{item:$subject} has commented on a {item:$object:review} you commented on.', 0, ''),
('liked_review', 'review', '{item:$subject} has commented on a {item:$object:review} you liked.', 0, '')
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

DELETE FROM `engine4_authorization_permissions` WHERE `type` = 'review';


-- ALL - except PUBLIC
-- auth_view, auth_comment, auth_html, auth_htmlattrs
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'featured' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');  
  
-- create
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');   
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'reviewed' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');    
  
-- ADMIN, MODERATOR
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view, delete, edit, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'review' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');


