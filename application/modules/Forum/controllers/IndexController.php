<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 9282 2011-09-21 00:42:22Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Forum_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    if ( !$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid() ) {
      return;
    }

    $categoryTable = Engine_Api::_()->getItemTable('forum_category');
    $this->view->categories = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));
    
    $forumTable = Engine_Api::_()->getItemTable('forum_forum');
    $forumSelect = $forumTable->select()
      ->order('order ASC')
      ;
    $forums = array();
    foreach( $forumTable->fetchAll() as $forum ) {
      if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'view') ) {
        $order = $forum->order;
        while( isset($forums[$forum->category_id][$order]) ) {
          $order++;
        }
        $forums[$forum->category_id][$order] = $forum;
        ksort($forums[$forum->category_id]);
      }
    }
    $this->view->forums = $forums;
    
    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
}