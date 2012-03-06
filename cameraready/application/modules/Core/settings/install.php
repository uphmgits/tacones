<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 8978 2011-06-14 22:51:05Z john $
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Install extends Engine_Package_Installer_Module
{
  protected function _runCustomQueries()
  {
    $db = $this->getDb();

    // Check for levels column
    try {
      $cols = $db->describeTable('engine4_core_pages');

      if( !isset($cols['levels']) ) {
        $db->query('ALTER TABLE `engine4_core_pages` ' .
            'ADD COLUMN `levels` text default NULL AFTER `layout`');
      } else if( $cols['levels']['DEFAULT'] != 'NULL' ) {
        $db->query('ALTER TABLE `engine4_core_pages` ' .
            'CHANGE COLUMN `levels` `levels` text default NULL AFTER `layout`');
      }

    } catch( Exception $e ) {
      throw $e;
    }

    // Get array of levels
    $select = new Zend_Db_Select($db);
    $levels = $select
      ->from('engine4_authorization_levels', 'level_id')
      ->query()
      ->fetchAll(Zend_Db::FETCH_COLUMN)
      ;
    $levels = Zend_Json::encode($levels);
    
    // assign levels json to any pages missing it
    try {
      $db->update('engine4_core_pages', array(
        'levels' => $levels,
      ), array(
        'custom = ?' => 1,
        'levels = \'\' OR levels = \'[]\' OR levels IS NULL',
      ));
    } catch( Exception $e ) {
      
    }

    // Remove public column for adcampaigns
    $cols = $db->describeTable('engine4_core_adcampaigns');
    if( isset($cols['public']) ) {
      $publicLevelId = $db->select()
        ->from('engine4_authorization_levels', 'level_id')
        ->where('flag = ?', 'public')
        ->limit(1)
        ->query()
        ->fetchColumn();
      
      $publicAdCampaigns = $db->select()
        ->from('engine4_core_adcampaigns')
        ->where('public = ?', 1)
        ->query()
        ->fetchAll()
        ;

      if( $publicLevelId && $publicAdCampaigns ) {
        foreach( $publicAdCampaigns as $publicAdCampaign ) {
          if( empty($publicAdCampaign['level']) ||
              !($levels = Zend_Json::decode($publicAdCampaign['level'])) ||
              !is_array($levels) ) {
            $levels = array();
          }
          if( !in_array($publicLevelId, $levels) ) {
            $levels[] = $publicLevelId;
            $db->update('engine4_core_adcampaigns', array(
              'level' => Zend_Json::encode($levels),
            ), array(
              'adcampaign_id = ?' => $publicAdCampaign['adcampaign_id'],
            ));
          }
        }
      }

      $db->query('ALTER TABLE `engine4_core_adcampaigns` DROP COLUMN `public`');
    }



    // Update all ip address to ipv6
    $this->_convertToIPv6($db, 'engine4_core_nodes', 'ip', false);
    $this->_convertToIPv6($db, 'engine4_core_bannedips', 'start', false);
    $this->_convertToIPv6($db, 'engine4_core_bannedips', 'stop', false);
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