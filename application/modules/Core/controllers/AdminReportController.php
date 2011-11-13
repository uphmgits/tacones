<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminReportController.php 8988 2011-06-15 01:35:25Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminReportController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    if( !defined('_ENGINE_ADMIN_NEUTER') || !_ENGINE_ADMIN_NEUTER ) {
      $this->_helper->requireUser();
    }
  }

  public function indexAction()
  {
    // Make form
    $this->view->formFilter = $formFilter = new Core_Form_Admin_Filter();

    // Process form
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if( empty($filterValues['order']) ) {
      $filterValues['order'] = 'report_id';
    }
    if( empty($filterValues['direction']) ) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;

    // Get paginator
    $table = Engine_Api::_()->getItemTable('core_report');
    $select = $table->select()
      ->order($filterValues['order'] . ' ' . $filterValues['direction']);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(2);
  }

  public function viewAction()
  {
    // first get the item and then redirect admin to the item page
    $this->view->id = $id = $this->_getParam('id', null);
    $report = Engine_Api::_()->getItem('core_report', $id);
    $item = Engine_Api::_()->getItem($report->subject_type, $report->subject_id);
    if( $item ) {
      $this->_redirectCustom($item->getHref());
    } else {
      $this->view->missing = true;
    }
  }
  
  public function deleteAction()
  {
    $this->view->id = $id = $this->_getParam('id', null);
    $report = Engine_Api::_()->getItem('core_report', $id);

    // Save values
    if( $this->getRequest()->isPost() )
    {
      $report->delete();
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      //$form->addMessage('Changes Saved!');
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
      foreach ($ids_array as $id){
        $report = Engine_Api::_()->getItem('core_report', $id);
        if( $report ) {
          $report->delete();
        }
      }

      //$announcement->delete();
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }

  public function actionAction()
  {
    // Check report ID and report
    $report_id = $this->_getParam('id', $this->_getParam('report_id'));
    if( !$report_id ) {
      $this->view->closeSmoothbox = true;
      return;
    }

    $report = Engine_Api::_()->getItem('core_report', $report_id);
    if( !$report ) {
      $this->view->closeSmoothbox = true;
      return;
    }

    // Get subject
    try {
      $this->view->subject = $subject = $report->getSubject();
    } catch( Exception $e ) {
      $this->view->subject = $subject = null;
    }

    // Get subject owner
    if( $subject instanceof Core_Model_Item_Abstract ) {
      try {
        $this->view->subjectOwner = $subjectOwner = $subject->getOwner('user');
      } catch( Exception $e ) {
        // Silence
        $this->view->subjectOwner = $subjectOwner = null;
      }
    } else {
      $this->view->subjectOwner = $subjectOwner = null;
    }

    // Get member
    if( $subject instanceof User_Model_User ) {
      $user = $subject;
    } else if( $subjectOwner instanceof User_Model_User ) {
      $user = $subjectOwner;
    } else {
      $user = null;
    }

    // Get member level
    if( $user ) {
      $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
      if( $level->type == 'admin' ) {
        $user = null; // Can't delete admins
      }
    }

    // Make form
    $this->view->form = $form = new Core_Form_Admin_Report_Action();

    if( !$subject ) {
      $form->removeElement('action');
      $form->removeElement('poster_action');
      $form->removeElement('ban');
    } else if( $subject instanceof User_Model_User ) {
      $form->removeElement('action');
      $form->getElement('action_poster')->setLabel('Action');
      $form->getElement('ban')->setLabel('Ban IP Address?');
    } else if( !$subjectOwner || !$user ) {
      $form->removeElement('ban');
      $form->removeElement('action_poster');
    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $values = $form->getValues();

    // Process action
    if( !empty($values['action']) ) {
      if( $values['action'] == 'delete' && $subject instanceof Core_Model_Item_Abstract ) {
        $subject->delete();
      }
    }

    // Process poster action
    if( !empty($values['action_poster']) && $user ) {
      $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
      if( $level->type == 'admin' ) {
        // Ignore
      } else {
        if( $values['action_poster'] == 'delete' ) {
          $user->delete();
        } else if( $values['action_poster'] == 'disable' ) {
          $user->enabled = $user->approved = false;
          $user->save();
        }
      }
    }

    // Process ban
    if( !empty($values['ban']) ) {
      if( $user instanceof User_Model_User ) {
        $bannedIpsTable = Engine_Api::_()->getDbtable('BannedIps', 'core');
        if( !empty($user->lastlogin_ip) ) {
          $bannedIpsTable->addAddress($user->lastlogin_ip);
        }
        if( !empty($user->signup_ip) ) {
          $bannedIpsTable->addAddress($user->signup_ip);
        }
      }
    }

    // Process dismiss
    if( !empty($values['dismiss']) ) {
      $report->delete();
    }

    // Done
    $this->view->closeSmoothbox = true;
  }
}