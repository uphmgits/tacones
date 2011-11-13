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
class Marketplace_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_manage');

    $page=$this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator(array(
      'orderby' => 'marketplace_id',
    ));
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->marketplace_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $marketplace = Engine_Api::_()->getItem('marketplace', $id);
        // delete the blog entry into the database
        $marketplace->delete();
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
    $this->renderScript('admin-manage/delete.tpl');
  }

  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $marketplace = Engine_Api::_()->getItem('marketplace', $id);
        if( $marketplace ) $marketplace->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }

  public function reportsAction() {
        if( !$this->_helper->requireUser()->isValid() ) return;
// $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
//      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_categories');
   // $this->view->navigation = $this->getNavigation();
$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_reports');
    $page = $this->_getParam('page', 1);


    $this->view->formFilter = $formFilter = new Marketplace_Form_Filter();//User_Form_Admin_Manage_Filter();
   // $page = $this->_getParam('page', 1);

    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('orders', 'marketplace');
    $select = $table->select();

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'order_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);


//    $values = array(//array_merge(array(
//      'order' => 'order_id',
//      'order_direction' => 'DESC',
//    );//, $values);

   // $this->view->assign($values);
   // $select = '';
 //   $select->order(( !empty($values['order']) ? $values['order'] : 'order_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

//    if( !empty($values['level_id']) )
//    {
//      $select->where('level_id = ?', $values['level_id'] );
//    }

  //$select->where('owner_id = ?',  $viewer->getIdentity());
  //
  //$select->where('');
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }
}