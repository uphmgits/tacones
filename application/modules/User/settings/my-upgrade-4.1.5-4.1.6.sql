
CREATE TABLE IF NOT EXISTS `engine4_user_settings` (
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_user_twitter` (
  `user_id` int(10) unsigned NOT NULL,
  `twitter_uid` bigint(20) unsigned NOT NULL,
  `twitter_token` varchar(255) NOT NULL default '',
  `twitter_secret` varchar(255) NOT NULL default '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `twitter_uid` (`twitter_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_twitter', 'user', 'Twitter Integration', '', '{"route":"admin_default", "action":"twitter", "controller":"settings", "module":"user"}', 'core_admin_main_settings', '', 4)
;

ALTER TABLE `engine4_user_facebook`
  ADD COLUMN `access_token` varchar(255) NOT NULL default '',
  ADD COLUMN `code` varchar(255) NOT NULL default '',
  ADD COLUMN `expires` bigint(20) unsigned NOT NULL default '0' ;

DROP TABLE IF EXISTS `engine4_user_online`;
CREATE TABLE IF NOT EXISTS `engine4_user_online` (
  `ip` varbinary(16) NOT NULL,
  `user_id` int(11) unsigned NOT NULL default '0',
  `active` datetime NOT NULL,
  PRIMARY KEY  (`ip`,`user_id`),
  KEY `LOOKUP` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_user_logins`
 ADD COLUMN `source` varchar(64) default NULL
 AFTER `state` ;
