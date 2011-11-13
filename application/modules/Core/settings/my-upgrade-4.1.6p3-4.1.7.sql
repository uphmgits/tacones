
DROP TABLE IF EXISTS `engine4_core_migrations`;
CREATE TABLE IF NOT EXISTS `engine4_core_migrations` (
  `package` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `current` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`package`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_serviceproviders`
--

DROP TABLE IF EXISTS `engine4_core_serviceproviders`;
CREATE TABLE IF NOT EXISTS `engine4_core_serviceproviders` (
  `serviceprovider_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(128) NOT NULL,
  `type` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `class` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`serviceprovider_id`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_core_serviceproviders`
--

INSERT IGNORE INTO `engine4_core_serviceproviders` (`title`, `type`, `name`, `class`, `enabled`) VALUES
('MySQL', 'database', 'mysql', 'Engine_ServiceLocator_Plugin_Database_Mysql', 1),
('PDO MySQL', 'database', 'mysql_pdo', 'Engine_ServiceLocator_Plugin_Database_MysqlPdo', 1),
('MySQLi', 'database', 'mysqli', 'Engine_ServiceLocator_Plugin_Database_Mysqli', 1),
('File', 'cache', 'file', 'Engine_ServiceLocator_Plugin_Cache_File', 1),
('APC', 'cache', 'apc', 'Engine_ServiceLocator_Plugin_Cache_Apc', 1),
('Memcache', 'cache', 'memcached', 'Engine_ServiceLocator_Plugin_Cache_Memcached', 1),
('Simple', 'captcha', 'image',  'Engine_ServiceLocator_Plugin_Captcha_Image', 1),
('ReCaptcha', 'captcha', 'recaptcha',  'Engine_ServiceLocator_Plugin_Captcha_Recaptcha', 1),
('SMTP', 'mail', 'smtp', 'Engine_ServiceLocator_Plugin_Mail_Smtp', 1),
('Sendmail', 'mail', 'sendmail', 'Engine_ServiceLocator_Plugin_Mail_Sendmail', 1),
('GD', 'image', 'gd', 'Engine_ServiceLocator_Plugin_Image_Gd', 1),
('Imagick', 'image', 'imagick', 'Engine_ServiceLocator_Plugin_Image_Imagick', 1),
('Akismet', 'akismet', 'standard', 'Engine_ServiceLocator_Plugin_Akismet', 1);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_services`
--

DROP TABLE IF EXISTS `engine4_core_services`;
CREATE TABLE IF NOT EXISTS `engine4_core_services` (
  `service_id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `profile` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default 'default',
  `config` text NOT NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`service_id`),
  UNIQUE KEY `type` (`type`, `profile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_core_services`
--


-- --------------------------------------------------------

--
-- Table structure for table `engine4_core_servicetypes`
--

DROP TABLE IF EXISTS `engine4_core_servicetypes`;
CREATE TABLE IF NOT EXISTS `engine4_core_servicetypes` (
  `servicetype_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(128) NOT NULL,
  `type` varchar(128) character set latin1 collate latin1_general_ci NOT NULL,
  `interface` varchar(128) character set latin1 collate latin1_general_ci default NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`servicetype_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_core_servicetypes`
--

INSERT IGNORE INTO `engine4_core_servicetypes` (`title`, `type`, `interface`, `enabled`) VALUES
('Database', 'database', 'Zend_Db_Adapter_Abstract', 1),
('Cache', 'cache', 'Zend_Cache_Backend', 1),
('Captcha', 'captcha', 'Zend_Captcha_Adapter', 1),
('Mail Transport', 'mail', 'Zend_Mail_Transport_Abstract', 1),
('Image', 'image', 'Engine_Image_Adapter_Abstract', 1),
('Akismet', 'akismet', 'Zend_Service_Akismet', 1);
