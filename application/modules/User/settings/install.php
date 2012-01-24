<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $db = $this->getDb();

    // Run upgrades first to prevent issues with upgrading from older versions
    parent::onInstall();
    
    // Update all ip address to ipv6
    try {
      $this->_convertToIPv6($db, 'engine4_users', 'creation_ip', false);
      $this->_convertToIPv6($db, 'engine4_users', 'lastlogin_ip', true);
      $this->_convertToIPv6($db, 'engine4_user_logins', 'ip', false);
    } catch( Exception $e ) {
      $this->_error('Query failed with error: ' . $e->getMessage());
    }
  }

  protected function _convertToIPv6($db, $table, $column, $isNull = false)
  {
    // Note: this group of functions will convert an IPv4 address to the new
    // IPv6-compatibly representation
    // ip = UNHEX(CONV(ip, 10, 16))

    // Detect if this is a 32bit system
    $is32bit = ( ip2long('200.200.200.200') < 0 );
    $offset = ( $is32bit ? '4294967296' : '0' );

    // Describe
    $cols = $db->describeTable($table);

    // Update
    if( isset($cols[$column]) && $cols[$column]['DATA_TYPE'] != 'varbinary' ) {
      $temporaryColumn = $column . '_tmp6';
      // Drop temporary column if it already exists
      if( isset($cols[$temporaryColumn]) ) {
        $db->query(sprintf('ALTER TABLE `%s` DROP COLUMN `%s`', $table, $temporaryColumn));
      }
      // Create temporary column
      $db->query(sprintf('ALTER TABLE `%s` ADD COLUMN `%s` varbinary(16) default NULL', $table, $temporaryColumn));
      // Copy and convert data
      $db->query(sprintf('UPDATE `%s` SET `%s` = UNHEX(CONV(%s + %u, 10, 16)) WHERE `%s` IS NOT NULL', $table, $temporaryColumn, $column, $offset, $column));
      // Drop old column
      $db->query(sprintf('ALTER TABLE `%s` DROP COLUMN `%s`', $table, $column));
      // Rename new column
      $db->query(sprintf('ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varbinary(16) %s', $table, $temporaryColumn, $column, ($isNull ? 'default NULL' : 'NOT NULL')));
    }
  }
}
