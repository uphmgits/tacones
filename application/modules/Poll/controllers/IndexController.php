<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 9398 2011-10-18 20:57:58Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Get subject
    $poll = null;
    if( null !== ($pollIdentity = $this->_getParam('poll_id')) ) {
      $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
      if( null !== $poll ) {
        Engine_Api::_()->core()->setSubject($poll);
      }
    }

    // Get viewer
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    // only show polls if authorized
    $resource = ( $poll ? $poll : 'poll' );
    $viewer = ( $viewer && $viewer->getIdentity() ? $viewer : null );
    if( !$this->_helper->requireAuth()->setAuthParams($resource, $viewer, 'view')->isValid() ) {
      return;
    }
  }

  public function browseAction()
  {
    // Prepare
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');
    
    // Get form
    $this->view->form = $form = new Poll_Form_Search();

    // Process form
    $values = array('browse' => 1);
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    }
    $this->view->formValues = array_filter($values);

    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);

    // Make paginator
    $currentPageNumber = $this->_getParam('page', 1);
    $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);
    
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
    $paginator
      ->setItemCountPerPage($itemCountPerPage)
      ->setCurrentPageNumber($currentPageNumber)
      ;

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }
  
  public function manageAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid() ) {
      return;
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_quick');

    // Get form
    $this->view->form = $form = new Poll_Form_Search();
    $form->removeElement('show');

    // Process form
    $this->view->owner = $owner = Engine_Api::_()->user()->getViewer();
    $this->view->user_id = $owner->getIdentity();
    $values = array();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    }
    $this->view->formValues = array_filter($values);
    $values['user_id'] = $owner->getIdentity();

    // Make paginator
    $currentPageNumber = $this->_getParam('page', 1);
    $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);

    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
    $paginator
      ->setItemCountPerPage($itemCountPerPage)
      ->setCurrentPageNumber($currentPageNumber)
      ;

    // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('poll', null, 'create')->isValid() ) {
      return;
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_main');

    $this->view->options = array();
    $this->view->maxOptions = $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.maxoptions', 15);
    $this->view->form = $form = new Poll_Form_Create();

    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Check options
    $options = (array) $this->_getParam('optionsArray');
    $options = array_filter(array_map('trim', $options));
    $options = array_slice($options, 0, $max_options);
    $this->view->options = $options;
    if( empty($options) || !is_array($options) || count($options) < 2 ) {
      return $form->addError('You must provide at least two possible answers.');
    }
    foreach( $options as $index => $option ) {
      if( strlen($option) > 80 ) {
        $options[$index] = Engine_String::substr($option, 0, 80);
      }
    }

    // Process
    $pollTable = Engine_Api::_()->getItemTable('poll');
    $pollOptionsTable = Engine_Api::_()->getDbtable('options', 'poll');
    $db = $pollTable->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();
      
      // Create poll
      $poll = $pollTable->createRow();
      $poll->setFromArray($values);
      $poll->save();

      // Create options
      $censor = new Engine_Filter_Censor();
      $html = new Engine_Filter_HtmlSpecialChars();
      
      foreach( $options as $option ) {
        $option = $censor->filter($html->filter($option));
        $pollOptionsTable->insert(array(
          'poll_id' => $poll->getIdentity(),
          'poll_option' => $option,
        ));
      }

      // Privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = array('everyone');
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = array('everyone');
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($poll, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($poll, $role, 'comment', ($i <= $commentMax));
      }

      $auth->setAllowed($poll, 'registered', 'vote', true);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    // Process activity
    $db = Engine_Api::_()->getDbTable('polls', 'poll')->getAdapter();
    $db->beginTransaction();
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(Engine_Api::_()->user()->getViewer(), $poll, 'poll_new');
      if( $action ) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $poll);
      }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    // Redirect
    return $this->_helper->redirector->gotoUrl($poll->getHref(), array('prependBase' => false));
  }
}