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
 
 
class Review_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_admin_main', array(), 'review_admin_main_manage');

      
    $this->view->formFilter = $formFilter = new Review_Form_Admin_Manage_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    $values = Engine_Api::_()->review()->filterEmptyParams($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->review()->getReviewsPaginator($values);
    //$this->view->paginator->setItemCountPerPage((int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.page', 10));
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;
    /*
    foreach ($this->view->paginator as $review)
    {
      $review->votes()->updatePreferenceCountKeys();
    }
    */
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
        $review = Engine_Api::_()->getItem('review', $id);
        if( $review ) $review->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }  
  
  public function featuredAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->review_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $review = Engine_Api::_()->getItem('review', $id);
        
        $review->featured = $this->_getParam('featured') == 'yes' ? 1 : 0;
        $review->save();
        
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  public function deleteAction()
  {
    // In smoothbox
    $id = $this->_getParam('id');
    $this->view->review_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $review = Engine_Api::_()->getItem('review', $id);
        $review->delete();
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
  }

}