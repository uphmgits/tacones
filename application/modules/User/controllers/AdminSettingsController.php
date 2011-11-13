<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 9273 2011-09-20 20:58:59Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    return $this->_helper->redirector->gotoRoute(array(
      'route' => 'admin_default',
      'module' => 'authorization',
      'controller' => 'level',
      'action' => 'edit'
    ));
  }

  public function generalAction()
  {

  }

  public function friendsAction()
  {
    $form = new User_Form_Admin_Settings_Friends();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $form->setMethod("POST");
    $this->view->form = $form;
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $form->saveValues();
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function facebookAction()
  {
    $form = $this->view->form = new User_Form_Admin_Facebook();
    $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->core_facebook);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    Engine_Api::_()->getApi('settings', 'core')->core_facebook = $form->getValues();
    $form->addNotice('Your changes have been saved.');
  }

  public function twitterAction()
  {
    // Get form
    $form = $this->view->form = new User_Form_Admin_Twitter();
    $form->populate((array) Engine_Api::_()->getApi('settings', 'core')->core_twitter);

    // Get classes
    include_once 'Services/Twitter.php';
    include_once 'HTTP/OAuth/Consumer.php';

    if( !class_exists('Services_Twitter', false) ||
        !class_exists('HTTP_OAuth_Consumer', false) ) {
      return $form->addError('Unable to load twitter API classes');
    }

    // Check data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    if( empty($values['key']) || empty($values['secret']) ) {
      $values['key'] = '';
      $values['secret'] = '';
    } else {

      // Try to check credentials
      try {
        $twitter = new Services_Twitter();
        $oauth = new HTTP_OAuth_Consumer($values['key'], $values['secret']);
        //$twitter->setOAuth($oauth);
        $oauth->getRequestToken('http://twitter.com/oauth/request_token');
        $oauth->getAuthorizeUrl('http://twitter.com/oauth/authorize');
        
      } catch( Exception $e ) {
        return $form->addError($e->getMessage());
      }
    }

    // Okay
    Engine_Api::_()->getApi('settings', 'core')->core_twitter = $form->getValues();
    $form->addNotice('Your changes have been saved.');
  }

  public function levelAction()
  {
    return $this->_helper->redirector->gotoRoute(array(
      'module' => 'authorization',
      'controller' => 'level',
      'action' => 'edit'
    ));
  }
}
