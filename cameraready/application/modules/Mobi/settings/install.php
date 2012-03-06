<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 9405 2011-10-18 23:07:04Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addMobiSiteHeader();
    $this->_addMobiSiteFooter();
    $this->_addMobiHomePage();
    $this->_addMobiUserHomePage();
    $this->_addMobiUserProfilePage();
    $this->_addMobiEventProfilePage();
    $this->_addMobiGroupProfilePage();
    
    parent::onInstall();
  }

    
  protected function _addMobiSiteHeader()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'header_mobi')
      ->limit(1);
    
    $info = $select->query()->fetch();
    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'header_mobi',
        'displayname' => 'Mobile Site Header',
        'title' => 'Mobile Site Header',
        'description' => 'This is the mobile site header.',
        'custom' => 0,
        'fragment' => 1
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.menu-logo',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mobi.mobi-menu-main',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));

    }
  }
  
  protected function _addMobiSiteFooter()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'footer_mobi')
      ->limit(1);
    
    $info = $select->query()->fetch();
    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'footer_mobi',
        'displayname' => 'Mobile Site Footer',
        'title' => 'Mobile Site Footer',
        'description' => 'This is the mobile site footer.',
        'custom' => 0,
        'fragment' => 1
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mobi.mobi-footer',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));

    }
  }
  
  protected function _addMobiHomePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mobi_index_index')
      ->limit(1);
    
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mobi_index_index',
        'displayname' => 'Mobile Home Page',
        'title' => 'Mobile Home Page',
        'description' => 'This is the mobile homepage.',
        'custom' => 0,
        'layout' => 'default',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'user.login-or-signup',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
    }
  }
  
  protected function _addMobiUserHomePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mobi_index_userhome')
      ->limit(1);
    
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mobi_index_userhome',
        'displayname' => 'Mobile Member Home Page',
        'title' => 'Mobile Member Home Page',
        'description' => 'This is the mobile member homepage.',
        'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'activity.feed',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
    }
  }
  
  protected function _addMobiUserProfilePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mobi_index_profile')
      ->limit(1);
    
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mobi_index_profile',
        'displayname' => 'Mobile Member Profile',
        'title' => 'Mobile Member Profile',
        'description' => 'This is the mobile verison of a member profile.',
        'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'user.profile-photo',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'user.profile-status',
        'parent_content_id' => $middle_id,
        'order' => 4,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'mobi.mobi-profile-options',
        'parent_content_id' => $middle_id,
        'order' => 5,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => 6,
        'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'activity.feed',
        'parent_content_id' => $tab_id,
        'order' => 7,
        'params' => '{"title":"What\'s New"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'user.profile-fields',
        'parent_content_id' => $tab_id,
        'order' => 8,
        'params' => '{"title":"Info"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'user.profile-friends',
        'parent_content_id' => $tab_id,
        'order' => 9,
        'params' => '{"title":"Friends","titleCount":true}',
      ));
    }
  }
  
  protected function _addMobiEventProfilePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'event')
      ->limit(1);
      ;
    $event_module = $select->query()->fetch();

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mobi_event_profile')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) && !empty($event_module) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mobi_event_profile',
        'displayname' => 'Mobile Event Profile',
        'title' => 'Mobile Event Profile',
        'description' => 'This is the mobile verison of an event profile.',
        'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'event.profile-status',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'event.profile-photo',
        'parent_content_id' => $middle_id,
        'order' => 4,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'event.profile-info',
        'parent_content_id' => $middle_id,
        'order' => 5,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'event.profile-rsvp',
        'parent_content_id' => $middle_id,
        'order' => 6,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => 7,
        'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'activity.feed',
        'parent_content_id' => $tab_id,
        'order' => 8,
        'params' => '{"title":"What\'s New"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'event.profile-members',
        'parent_content_id' => $tab_id,
        'order' => 9,
        'params' => '{"title":"Guests","titleCount":true}',
      ));
    }
  }
  
  protected function _addMobiGroupProfilePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'group')
      ->limit(1);
      ;
    $group_module = $select->query()->fetch();

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'mobi_group_profile')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) && !empty($group_module) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'mobi_group_profile',
        'displayname' => 'Mobile Group Profile',
        'title' => 'Mobile Group Profile',
        'description' => 'This is the mobile verison of a group profile.',
        'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'group.profile-status',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'group.profile-photo',
        'parent_content_id' => $middle_id,
        'order' => 4,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'group.profile-info',
        'parent_content_id' => $middle_id,
        'order' => 5,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => 6,
        'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'activity.feed',
        'parent_content_id' => $tab_id,
        'order' => 7,
        'params' => '{"title":"What\'s New"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'group.profile-members',
        'parent_content_id' => $tab_id,
        'order' => 8,
        'params' => '{"title":"Members","titleCount":true}',
      ));
    }
  }
}
