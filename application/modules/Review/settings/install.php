<?php



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
 
 
 
class Review_Installer extends Engine_Package_Installer_Module
{
  public function addUserProfileSide()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'review.profile-rating')
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

      // left_id (may not always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'left')
        ->limit(1);
      $left_id = $select->query()->fetchObject();
      if ($left_id && $left_id->content_id)
          $left_id  = $left_id->content_id;
      else
          $left_id  = null;

      if ($left_id)    
      {
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'review.profile-rating',
          'parent_content_id' => $left_id,
          'order'   => 5,
          'params'  => '',
        ));
      }
      
    }
  }
  
  public function addUserProfileTab()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // review.profile-reviews
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'review.profile-reviews')
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
        'name'    => 'review.profile-reviews',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Reviews","titleCount":true,"max":5,"showdetails":0}',
      ));

    }
  }
  
  
  public function addHomePage()
  {
    // Review Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'review_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'review_index_index',
        'displayname' => 'Review Home Page',
        'title' => 'Reviews Home Page',
        'description' => 'This is the home page for reviews.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.list-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
      // ------ MAIN :: LEFT WIDGETS  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.categories',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"","max":5,"showphoto":1,"showstars":1,"showdetails":1}',
      ));  
      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.featured-review',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Featured Review"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.list-reviews',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Recent Reviews","max":10,"order":"recent"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'review.popular-tags',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags","max":"50","order":"text"}',
      ));
      
    }
  }  
  
  
  public function onInstall()
  {
    $this->addUserProfileTab();
    $this->addUserProfileSide();
    $this->addHomePage();
    
    parent::onInstall();
  }
}
