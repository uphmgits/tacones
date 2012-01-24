
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('core.twitter.enable', 'none'),
('core.twitter.key', ''),
('core.twitter.secret', '')
;

ALTER TABLE `engine4_core_bannedips`
  DROP KEY `start` ;

ALTER TABLE `engine4_core_bannedips`
  ADD UNIQUE KEY `start` (`start`, `stop`) ;

ALTER TABLE `engine4_core_bannedemails`
  DROP KEY `email` ;

ALTER TABLE `engine4_core_bannedemails`
  ADD UNIQUE KEY `email` (`email`) ;

ALTER TABLE `engine4_core_bannedemails`
  DROP COLUMN `whitelist` ;

DROP TABLE IF EXISTS `engine4_core_bannedusernames`;
CREATE TABLE IF NOT EXISTS `engine4_core_bannedusernames` (
  `bannedusername_id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY  (`bannedusername_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
