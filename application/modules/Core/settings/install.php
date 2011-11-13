<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 9405 2011-10-18 23:07:04Z john $
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
    
    $this->_addContactPage();
    $this->_addPrivacyPage();
    $this->_addTermsOfServicePage();
  }
  
  protected function _addPrivacyPage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'core_help_privacy')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    if (!$page_id) {
       // Insert page
        $db->insert('engine4_core_pages', array(
          'name' => 'core_help_privacy',
          'displayname' => 'Privacy Page',
          'title' => 'Privacy Policy',
          'description' => 'This is the privacy policy page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

       // Insert main
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
        ));
        $main_id = $db->lastInsertId();

        // Insert middle
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
        ));
        $middle_id = $db->lastInsertId();

        // Insert content
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
        ));
    }
    
    return $this;
  }
  
  protected function _addTermsOfServicePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'core_help_terms')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    if (!$page_id) {
       // Insert page
        $db->insert('engine4_core_pages', array(
          'name' => 'core_help_terms',
          'displayname' => 'Terms of Service Page',
          'title' => 'Terms of Service',
          'description' => 'This is the terms of service page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

       // Insert main
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
        ));
        $main_id = $db->lastInsertId();

        // Insert middle
        $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
        ));
        $middle_id = $db->lastInsertId();

        // Insert content
        $db->insert('engine4_core_content', array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
        ));
    }
    
    return $this;
  }
  
  protected function _addContactPage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'core_help_contact')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    if (!$page_id) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'core_help_contact',
        'displayname' => 'Contact Page',
        'title' => 'Contact Us',
        'description' => 'This is the contact page',
        'provides' => 'no-viewer;no-subject',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
     // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 1,
      ));
    }
    return $this;
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