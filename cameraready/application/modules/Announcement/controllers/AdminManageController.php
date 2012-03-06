<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Announcement
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 9076 2011-07-21 02:11:10Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Announcement
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Announcement_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->formFilter = $formFilter = new Announcement_Form_Admin_Filter();
    $page = $this->_getParam('page', 1);

    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      $paginator = Engine_Api::_()->announcement()->getPaginator($values);
      if ($values['orderby'] && $values['orderby_direction'] != 'DESC') {
        $this->view->orderby = $values['orderby'];
      }
    } else {
      $paginator = Engine_Api::_()->announcement()->getPaginator();
    }
    
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }
  
  
  public function createAction()
  {
    $this->view->form = $form = new Announcement_Form_Admin_Create();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $params = $form->getValues();
      $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
      $announcement = Engine_Api::_()->getDbtable('announcements', 'announcement')->createRow();
      $announcement->setFromArray($params);
      $announcement->save();

      //increment statistics
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('announcement.creations');

      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function editAction()
  {
    $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('announcement', $id);
    
    $this->view->form = $form = new Announcement_Form_Admin_Edit();
    $form->populate($announcement->toArray());

    // Save values
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $params = $form->getValues();
      $announcement->setFromArray($params);
      $announcement->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function deleteAction()
  {
    //$this->view->form = $form = new Announcement_Form_Admin_Edit();
    $this->view->id = $id = $this->_getParam('id', null);
    $announcement = Engine_Api::_()->getItem('announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() )
    {
      $announcement->delete();
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function deleteselectedAction()
  {
    //$this->view->form = $form = new Announcement_Form_Admin_Edit();
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    //$announcement = Engine_Api::_()->getItem('announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $announcement = Engine_Api::_()->getItem('announcement', $id);
        if( $announcement ) $announcement->delete();
      }
      
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }
}