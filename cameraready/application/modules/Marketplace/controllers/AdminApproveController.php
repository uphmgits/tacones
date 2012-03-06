<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 8306 2011-01-25 22:31:44Z jung $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Marketplace_AdminApproveController extends Core_Controller_Action_Admin
{
  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_approvephoto');

    $photosTable = Engine_Api::_()->getDbtable('photos', 'marketplace'); 
    $photosTableName = $photosTable->info('name');

    $select = $photosTable->select()->where('approved_photo = 0')->order('modified_date DESC');

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      if( isset($values['approve']) ) {
        foreach ($values as $key => $value) {
          if ($key == 'checked_' . $value) {
            $photo = Engine_Api::_()->getItem('marketplace_photo', $value);
            $photo->approved_photo = 1;
            $photo->save();
          }
        }
      }
      if( isset($values['delete']) ) {
        foreach ($values as $key => $value) {
          if ($key == 'checked_' . $value) {
            $photo = Engine_Api::_()->getItem('marketplace_photo', $value);
            $photo->delete();
          }
        }
      }
    }
   
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);

  }

  public function approveAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->photo_id = $id;

    // Check post
    if( $this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $photo = Engine_Api::_()->getItem('marketplace_photo', $id);
        $photo->approved_photo = 1;
        $photo->save();
        $db->commit();
      }

      catch( Exception $e ) {
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
    $this->renderScript('admin-approve/approve.tpl');
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->photo_id = $id;

    // Check post
    if( $this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $photo = Engine_Api::_()->getItem('marketplace_photo', $id);
        $photo->delete();
        $db->commit();
      }

      catch( Exception $e ) {
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
    $this->renderScript('admin-approve/delete.tpl');
  }

}
