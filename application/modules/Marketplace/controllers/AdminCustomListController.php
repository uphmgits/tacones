<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: AdminManageController.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_AdminCustomListController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_customlist');

    $page=$this->_getParam('page',1);
    $viewer = Engine_Api::_()->user()->getViewer();

    $marketplacesTable = Engine_Api::_()->getDbTable('marketplaces', 'marketplace');
    $marketplacesTableName = $marketplacesTable->info('name');

    $customlistTable = Engine_Api::_()->getDbTable('customlist', 'marketplace');
    $customlistTableName = $customlistTable->info('name');

    $subSelect = $customlistTable->select()->from($customlistTableName, "marketplace_id")->where("user_id = {$viewer->getIdentity()}");
    $select = $marketplacesTable->select()
                                ->where('marketplace_id NOT IN (?)', new Zend_Db_Expr($subSelect))
                                ->order('marketplace_id DESC')
    ;
    $this->view->paginator = Zend_Paginator::factory($select); 
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function unhideAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->marketplace_id = $id;

    // Check post
    $customlistTable = Engine_Api::_()->getDbTable('customlist', 'marketplace');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $customlistTable = Engine_Api::_()->getDbTable('customlist', 'marketplace');
      $viewer = Engine_Api::_()->user()->getViewer();
      $customlistTable->delete("marketplace_id = {$id} AND user_id = {$viewer->getIdentity()}");
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
    ));
  }

  public function hideAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->marketplace_id = $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();
    // Check post
    if( $this->getRequest()->isPost())
    {
      $customlistTable = Engine_Api::_()->getDbTable('customlist', 'marketplace');
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $customlistTable->insert(array('marketplace_id' => $id, 'user_id' => $viewer->getIdentity()));
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-custom-list/hide.tpl');
  }

  public function hideselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      $customlistTable = Engine_Api::_()->getDbTable('customlist', 'marketplace');
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
        foreach( $ids_array as $id ) {
          $customlistTable->insert(array('marketplace_id' => $id, 'user_id' => $viewerId));
        }
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }


}
