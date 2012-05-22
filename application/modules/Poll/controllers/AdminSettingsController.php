<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 9076 2011-07-21 02:11:10Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_admin_main', array(), 'poll_admin_main_settings');
    
    $this->view->form = $form = new Poll_Form_Admin_Settings_Global();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $form->populate($settings->poll);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      $settings->poll = $values;
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    $form->addNotice('Your changes have been saved.');
  }
  
  public function levelAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_admin_main', array(), 'poll_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Poll_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('poll', $id, array_keys($form->getValues())));

    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      // Set permissions
      $permissionsTable->setAllowed('poll', $id, $values);

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    $form->addNotice('Your changes have been saved.');
  }
}