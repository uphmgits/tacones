<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: install.php 7244 2011-03-19 01:49:53Z nexus $
 * @author     Nexus
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 
 * * 
 */

class Marketplace_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  { 
  
    $plugin = "Marketplace";
    $server = $_SERVER['SERVER_NAME'];
    $f = file("http://socialenginemarket.com/licenses/".md5($server.$plugin));

    if (trim($f[0]) != "1" && 0) {
    	return $this->_error('Please register your copy of plugin - <a class="smoothbox" href="http://socialenginemarket.com/user_product_register.php?plugin='.$plugin.'&server='.$server.'">Click Here</a>');  
    }
    
    
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
      ->where('name = ?', 'marketplace.profile-marketplaces')
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
        'name'    => 'marketplace.profile-marketplaces',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Marketplace","titleCount":true}',
      ));

    }

    parent::onInstall();
  }
}
 
 