<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PollController.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_PollController extends Core_Controller_Action_Standard
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

  public function closeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->getItem('poll', $this->_getParam('poll_id'));
    if( !Engine_Api::_()->core()->hasSubject('poll') ) {
      Engine_Api::_()->core()->setSubject($poll);
    }
    $this->view->poll = $poll;

    // Check auth
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams($poll, $viewer, 'edit')->isValid() ) {
      return;
    }

    // @todo convert this to post only

    $table = $poll->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $poll->closed = (bool) $this->_getParam('closed');
      $poll->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    if( !($returnUrl = $this->_getParam('return_url')) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'poll_general', true);
    } else {
      return $this->_redirect($returnUrl, array('prependBase' => false));
    }
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->getItem('poll', $this->getRequest()->getParam('poll_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($poll, null, 'delete')->isValid()) return;

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = $form = new Poll_Form_Delete();

    if( !$poll ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Poll doesn't exist or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $poll->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $poll->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your poll has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'poll_general', true),
      'messages' => Array($this->view->message)
    ));
  }
  
  public function editAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_main');

    // Setup
    $viewer = Engine_Api::_()->user()->getViewer();
    $poll = Engine_Api::_()->core()->getSubject('poll');

    // Get form
    $this->view->form = $form = new Poll_Form_Edit();
    $form->removeElement('title');
    $form->removeElement('description');
    $form->removeElement('options');

    // Prepare privacy
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    // Populate form with current settings
    $form->search->setValue($poll->search);
    foreach( $roles as $role ) {
      if( 1 === $auth->isAllowed($poll, $role, 'view') ) {
        $form->auth_view->setValue($role);
      }
      if( 1 === $auth->isAllowed($poll, $role, 'comment') ) {
        $form->auth_comment->setValue($role);
      }
    }

    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();

      // CREATE AUTH STUFF HERE
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

      $poll->search = (bool) $values['search'];
      $poll->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($poll) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'poll_general', true);
  }

  public function viewAction()
  {
    // Check auth
    if( !$this->_helper->requireSubject('poll')->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }

    $this->view->poll = $poll = Engine_Api::_()->core()->getSubject('poll');
    $this->view->owner = $owner = $poll->getOwner();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->pollOptions = $poll->getOptions();
    $this->view->hasVoted = $poll->viewerVoted();
    $this->view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
    $this->view->canVote = $poll->authorization()->isAllowed(null, 'vote');
    $this->view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
    
    if( !$owner->isSelf($viewer) ) {
      $poll->view_count++;
      $poll->save();
    }

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }

  public function voteAction()
  {
    // Check auth
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'vote')->isValid() ) {
      return;
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $option_id = $this->_getParam('option_id');
    $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);

    $poll = Engine_Api::_()->core()->getSubject('poll');
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$poll ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll does not seem to exist anymore.');
      return;
    }

    if( $poll->closed ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll is closed.');
      return;
    }

    if( $poll->hasVoted($viewer) && !$canChangeVote ) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have already voted on this poll, and are not permitted to change your vote.');
      return;
    }

    $db = Engine_Api::_()->getDbtable('polls', 'poll')->getAdapter();
    $db->beginTransaction();
    try {
      $poll->vote($viewer, $option_id);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }

    $this->view->success = true;
    $pollOptions = array();
    foreach( $poll->getOptions()->toArray() as $option ) {
      $option['votesTranslated'] = $this->view->translate(array('%s vote', '%s votes', $option['votes']), $this->view->locale()->toNumber($option['votes']));
      $pollOptions[] = $option;
    }
    $this->view->pollOptions = $pollOptions;
    $this->view->votes_total = $poll->vote_count;
  }
}