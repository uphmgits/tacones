<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: SettingsController.php 9334 2011-09-29 00:25:38Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_SettingsController extends Core_Controller_Action_User
{
  protected $_user;

  public function init()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id )
    {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    }
    else
    {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );
    
    // Set up navigation
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
    
    $contextSwitch = $this->_helper->contextSwitch;
    $contextSwitch
      //->addActionContext('reject', 'json')
      ->initContext();
  }

  public function generalAction()
  {
    // Config vars
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $user = Engine_Api::_()->core()->getSubject();
    $this->view->form = $form = new User_Form_Settings_General(array(
      'item' => $user
    ));

    // Set up profile type options
    /*
    $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    if( isset($aliasedFields['profile_type']) )
    {
      $options = $aliasedFields['profile_type']->getElementParams($user);
      unset($options['options']['order']);
      $form->accountType->setOptions($options['options']);
    }
    else
    { */
      $form->removeElement('accountType');
    /* } */
    
    // Removed disabled features
    if( $form->getElement('username') && (!Engine_Api::_()->authorization()->isAllowed('user', $user, 'username') ||
        Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username', 1) <= 0) ) {
      $form->removeElement('username');
    }

    // Facebook
    if( 'none' != $settings->getSetting('core.facebook.enable', 'none') ) {
      $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
      $facebook = $facebookTable->getApi();
      if( $facebook && $facebook->getUser() ) {
        $form->removeElement('facebook');
        $form->getElement('facebook_id')->setAttrib('checked', true);
      } else {
        $form->removeElement('facebook_id');
      }
    } else {
      // these should already be removed inside the form, but lets do it again.
      @$form->removeElement('facebook');
      @$form->removeElement('facebook_id');
    }

    // Twitter
    if( 'none' != $settings->getSetting('core.twitter.enable', 'none') ) {
      $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
      $twitter = $twitterTable->getApi();
      if( $twitter && $twitterTable->isConnected() ) {
        $form->removeElement('twitter');
        $form->getElement('twitter_id')->setAttrib('checked', true);
      } else {
        $form->removeElement('twitter_id');
      }
    } else {
      // these should already be removed inside the form, but lets do it again.
      @$form->removeElement('twitter');
      @$form->removeElement('twitter_id');
    }


    // Check if post and populate
    if( !$this->getRequest()->isPost() ) {
      $form->populate($user->toArray());
      
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    // Check if valid
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // -- Process --

    $values = $form->getValues();

    // Check email against banned list if necessary
    if( ($emailEl = $form->getElement('email')) &&
        isset($values['email']) &&
        $values['email'] != $user->email ) {
      $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
      if( $bannedEmailsTable->isEmailBanned($values['email']) ) {
        return $emailEl->addError('This email address is not available, please use another one.');
      }
    }

    // Check username against banned list if necessary
    if( ($usernameEl = $form->getElement('username')) &&
        isset($values['username']) &&
        $values['username'] != $user->username ) {
      $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');
      if( $bannedUsernamesTable->isUsernameBanned($values['username']) ) {
        return $usernameEl->addError('This profile address is not available, please use another one.');
      }
    }

    // Set values for user object
    $user->setFromArray($values);

    // If username is changed
    $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
    $user->setDisplayName($aliasValues);
    
    $user->save();

    
    // Update account type
    /*
    $accountType = $form->getValue('accountType');
    if( isset($aliasedFields['profile_type']) )
    {
      $valueRow = $aliasedFields['profile_type']->getValue($user);
      if( null === $valueRow ) {
        $valueRow = Engine_Api::_()->fields()->getTable('user', 'values')->createRow();
        $valueRow->field_id = $aliasedFields['profile_type']->field_id;
        $valueRow->item_id = $user->getIdentity();
      }
      $valueRow->value = $accountType;
      $valueRow->save();
    }
     *
     */

    // Update facebook settings
    if( isset($facebook) && $form->getElement('facebook_id') ) {
      if( $facebook->getUser() ) {
        if( empty($values['facebook_id']) ) {
          // Remove integration
          $facebookTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          $facebook->clearAllPersistentData();
        }
      }
    }

    // Update twitter settings
    if( isset($twitter) && $form->getElement('twitter_id') ) {
      if( $twitterTable->isConnected() ) {
        if( empty($values['twitter_id']) ) {
          // Remove integration
          $twitterTable->delete(array(
            'user_id = ?' => $user->getIdentity(),
          ));
          unset($_SESSION['twitter_token2']);
          unset($_SESSION['twitter_secret2']);
          unset($_SESSION['twitter_token']);
          unset($_SESSION['twitter_secret']);
        }
      }
    }
    
    // Send success message
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Settings saved.');
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
  }

  public function privacyAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $auth = Engine_Api::_()->authorization()->context;

    $this->view->form = $form = new User_Form_Settings_Privacy(array(
      'item' => $user,
    ));

    // Init blocked
    $this->view->blockedUsers = array();

    if( Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      foreach ($user->getBlockedUsers() as $blocked_user_id) {
        $this->view->blockedUsers[] = Engine_Api::_()->user()->getUser($blocked_user_id);
      }
    } else {
      $form->removeElement('blockList');
    }

    if( !Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search') ) {
      $form->removeElement('search');
    }


    // Hides options from the form if there are less then one option.
    if( count($form->privacy->options) <= 1 ) {
      $form->removeElement('privacy');
    }
    if( count($form->comment->options) <= 1 ) {
      $form->removeElement('comment');
    }

    // Populate form
    $form->populate($user->toArray());

    // Set up activity options
    if( $form->getElement('publishTypes') ) {
      $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
      unset($actionTypes['signup']);
      unset($actionTypes['postself']);
      unset($actionTypes['post']);
      unset($actionTypes['status']);
      $form->publishTypes->setMultiOptions($actionTypes);
      $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);
      $form->publishTypes->setValue($actionTypesEnabled);
    }
    
    // Check if post and populate
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $form->save();
    $user->setFromArray($form->getValues())
      ->save();

    // Update notification settings
    if( $form->getElement('publishTypes') ) {
      $publishTypes = $form->publishTypes->getValue();
      $publishTypes[] = 'signup';
      $publishTypes[] = 'post';
      $publishTypes[] = 'status';
      Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array) $publishTypes);
    }
  }

  public function passwordAction()
  {
    $user = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new User_Form_Settings_Password();
    $form->populate($user->toArray());

    if( !$this->getRequest()->isPost() ){
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Check conf
    if( $form->getValue('passwordConfirm') !== $form->getValue('password') ) {
      $form->getElement('passwordConfirm')->addError(Zend_Registry::get('Zend_Translate')->_('Passwords did not match'));
      return;
    }
    
    // Process form
    $userTable = Engine_Api::_()->getItemTable('user');
    $db = $userTable->getAdapter();

    // Check old password
    $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
    $select = $userTable->select()
      ->from($userTable, new Zend_Db_Expr('TRUE'))
      ->where('user_id = ?', $user->getIdentity())
      ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($form->getValue('oldPassword')))))
      ->limit(1)
      ;
    $valid = $select
      ->query()
      ->fetchColumn()
      ;

    if( !$valid ) {
      $form->getElement('oldPassword')->addError(Zend_Registry::get('Zend_Translate')->_('Old password did not match'));
      return;
    }

    
    // Save
    $db->beginTransaction();

    try {

      $user->setFromArray($form->getValues());
      $user->save();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
  }

  public function networkAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings');

    $viewer = Engine_Api::_()->user()->getViewer();

    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)
      ->order('engine4_network_networks.title ASC');
    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

    // Get networks to suggest
    $network_suggestions = array();
    $table = Engine_Api::_()->getItemTable('network');
    $select = $table->select()
      ->where('assignment = ?', 0)
      ->order('title ASC');

    if( null !== ($text = $this->_getParam('text', $this->_getParam('text'))))
    {
      $select->where('`'.$table->info('name').'`.`title` LIKE ?', '%'. $text .'%');
    }

    $data = array();
    foreach( $table->fetchAll($select) as $network )
    {
      if( !$network->membership()->isMember($viewer) )
      {
        $network_suggestions[] = $network;
      }
    }
    $this->view->network_suggestions = $network_suggestions;


    $this->view->form = $form = new User_Form_Settings_Network();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $form->getValue('join_id') ) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('join_id'));
      if( null === $network ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if( $network->assignment != 0 ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->addMember($viewer)
          ->setUserApproved($viewer)
          ->setResourceApproved($viewer);

        if (!$network->hide){
          // Activity feed item
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $network, 'network_join');
        }
      }
    } else if( $form->getValue('leave_id') ) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('leave_id'));
      if( null === $network ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if( $network->assignment != 0 ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->removeMember($viewer);
      }
    }

    $this->_helper->redirector->gotoRoute(array());
  }

  public function notificationsAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    
    // Build the different notification types
    $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
    $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
    $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

    $notificationTypesAssoc = array();
    $notificationSettingsAssoc = array();
    foreach( $notificationTypes as $type ) {
      if( in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user')) ) {
        $elementName = 'general';
        $category = 'General';
      } else if( isset($modules[$type->module]) ) {
        $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
        $category = $modules[$type->module]->title;
      } else {
        $elementName = 'misc';
        $category = 'Misc';
      }

      $notificationTypesAssoc[$elementName]['category'] = $category;
      $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

      if( in_array($type->type, $notificationSettings) ) {
        $notificationSettingsAssoc[$elementName][] = $type->type;
      }
    }

    ksort($notificationTypesAssoc);

    $notificationTypesAssoc = array_filter(array_merge(array(
      'general' => array(),
      'misc' => array(),
    ), $notificationTypesAssoc));

    // Make form
    $this->view->form = $form = new Engine_Form(array(
      'title' => 'Notification Settings',
      'description' => 'Which of the these do you want to receive email alerts about?',
    ));

    foreach( $notificationTypesAssoc as $elementName => $info ) {
      $form->addElement('MultiCheckbox', $elementName, array(
        'label' => $info['category'],
        'multiOptions' => $info['types'],
        'value' => (array) @$notificationSettingsAssoc[$elementName],
      ));
    }

    $form->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));

    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = array();
    foreach( $form->getValues() as $key => $value ) {
      if( !is_array($value) ) continue;
      
      foreach( $value as $skey => $svalue ) {
        if( !isset($notificationTypesAssoc[$key]['types'][$svalue]) ) {
          continue;
        }
        $values[] = $svalue;
      }
    }
    
    // Set notification setting
    Engine_Api::_()->getDbtable('notificationSettings', 'activity')
        ->setEnabledNotifications($user, $values);

    $form->addNotice('Your changes have been saved.');
  }

  public function deleteAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid() ) return;

    $this->view->isLastSuperAdmin   = false;
    if( 1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id ) {
      $this->view->isLastSuperAdmin = true;
    }

    // Form
    $this->view->form = $form = new User_Form_Settings_Delete();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $user->delete();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // Unset viewer, remove auth, clear session
    Engine_Api::_()->user()->setViewer(null);
    Zend_Auth::getInstance()->getStorage()->clear();
    Zend_Session::destroy();

    return $this->_helper->redirector->gotoRoute(array(), 'default', true);
  }
}