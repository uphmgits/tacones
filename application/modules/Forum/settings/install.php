<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 9405 2011-10-18 23:07:04Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Forum_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addUserProfileContent();
    $this->_addForumIndexPage();
    $this->_addForumViewPage();
    
    parent::onInstall();
  }
  
  protected function _addForumIndexPage()
  {
    $db = $this->getDb();
    
    // check page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'forum_index_index')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'forum_index_index',
        'displayname' => 'Forum Main Page',
        'title' => 'Forum Main',
        'description' => 'This is the main forum page.',
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
      ));
      $middle_id = $db->lastInsertId();
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
      ));
    }
  }
  
  protected function _addForumViewPage()
  {
    $db = $this->getDb();
    
    // check page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'forum_forum_view')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'forum_forum_view',
        'displayname' => 'Forum View Page',
        'title' => 'Forum View',
        'description' => 'This is the view forum page.',
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
      ));
      $middle_id = $db->lastInsertId();
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
      ));
    }
  }
  
  protected function _addUserProfileContent()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    //INSERT INTO `engine4_core_content` (`content_id`, `page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // forum.profile-forum-posts

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'forum.profile-forum-posts')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'forum.profile-forum-posts',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 9,
        'params'  => '{"title":"Forum Posts","titleCount":true}',
      ));
    }
  }
}