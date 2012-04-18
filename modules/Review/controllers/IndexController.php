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
 
 
class Review_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('review', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($review_id = (int) $this->_getParam('review_id')) &&
          null !== ($review = Engine_Api::_()->getItem('review', $review_id)) )
      {
        Engine_Api::_()->core()->setSubject($review);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'delete',
      'edit',
      'manage',
      'vote',
      'unvote',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'review',
      'edit' => 'review',
      'view' => 'review',
      'vote' => 'review',
    	'unvote' => 'review'
    ));
  }
  
  
  public function indexAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }
  
  // NONE USER SPECIFIC METHODS
  public function browseAction()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_quick'); 
    
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Review_Form_Search();


    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }
    
    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values);

    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    }    
          
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.perpage', 10);
    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    
  }

  
  public function manageAction()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_quick'); 
    
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Review_Form_Search();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'review_manage',true));
    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values);
    
    //$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
       
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.perpage', 10);
    $values['owner'] = $viewer->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

  }


  public function listUserAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    Engine_Api::_()->core()->clearSubject();
    if( 0 !== ($user_id = (int) $this->_getParam('id')) &&
        null !== ($user = Engine_Api::_()->getItem('user', $user_id)) )
    {
      Engine_Api::_()->core()->setSubject($user);
    }    
    
    if( !$this->_helper->requireSubject('user')->isValid() ) {
      return;
    }
    
    $this->view->form = $form = new Review_Form_Filter();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id'=>$user_id),'review_user',true));
    
    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
        
    
    $this->view->user = $user;

    $this->view->total_review = Engine_Api::_()->review()->getUserReviewCount($user);
    $this->view->average_rating = Engine_Api::_()->review()->getUserAverageRating($user);
    $this->view->distributions = Engine_Api::_()->review()->getUserReviewDistributions($user);     
    $this->view->total_recommend = Engine_Api::_()->review()->getUserRecommendCount($user);
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.perpage', 10);
    $values['user'] = $user;
    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    
   
    $this->view->viewer_review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $user);
    if ($this->view->viewer_review)
    {
      $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('review', null, 'edit')->checkRequire();
    }
    else
    {
      $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('review', null, 'create')->checkRequire();
    } 
    
    $values = array(
      'owner' => $user,
      'limit' => 5,
      'order' => 'random',
      //'recommend' => 1
    );
    $this->view->paginatorOwnerReview = Engine_Api::_()->review()->getReviewsPaginator($values);
    
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }
  
  
  public function listOwnerAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    Engine_Api::_()->core()->clearSubject();
    if( 0 !== ($owner_id = (int) $this->_getParam('id')) &&
        null !== ($owner = Engine_Api::_()->getItem('user', $owner_id)) )
    {
      Engine_Api::_()->core()->setSubject($owner);
    }    
    
    if( !$this->_helper->requireSubject('user')->isValid() ) {
      return;
    }
    
    $this->view->form = $form = new Review_Form_Filter();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id'=>$owner_id),'review_owner',true));
    
    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
    
    $this->view->owner = $owner;
    
    $this->view->total_review = Engine_Api::_()->review()->getOwnerReviewCount($owner);
    $this->view->average_rating = Engine_Api::_()->review()->getOwnerAverageRating($owner);
    $this->view->distributions = Engine_Api::_()->review()->getOwnerReviewDistributions($owner);     
    $this->view->total_recommend = Engine_Api::_()->review()->getOwnerRecommendCount($owner);
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.perpage', 10);
    $values['owner'] = $owner;
    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('review', null, 'create')->checkRequire();
    
    $values = array(
      'user' => $owner,
      'limit' => 5,
      'order' => 'random'
    );
    $this->view->paginatorUserReview = Engine_Api::_()->review()->getReviewsPaginator($values);
  }
  
  public function viewAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $review = Engine_Api::_()->core()->getSubject('review');

    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams($review, null, 'view')->isValid() ) {
      return;
    }

    $this->view->owner = $owner = $review->getOwner();
    $this->view->user = $user = $review->getUser();
    
    $this->view->canEdit = $this->_helper->requireAuth()->setAuthParams($review, null, 'edit')->checkRequire();
    $this->view->canDelete = $this->_helper->requireAuth()->setAuthParams($review, null, 'delete')->checkRequire();   

    if (!$owner->isSelf($viewer)) {
      $review->view_count++;
      $review->save();
    }
    
    // get tags
    $this->view->reviewTags = $review->tags()->getTagMaps();
    
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($review); 
    
    $this->view->total_review = Engine_Api::_()->getItemTable('review')->getReviewCount(array('user'=>$user));
    $this->view->average_rating = number_format(Engine_Api::_()->getItemTable('review')->getAverageRating(array('user'=>$user)),1);
    $this->view->distributions = Engine_Api::_()->getItemTable('review')->getDistributions(array('user'=>$user));        
    
    $this->view->owner_total_review = Engine_Api::_()->getItemTable('review')->getReviewCount(array('owner'=>$owner));
    $this->view->owner_average_rating = number_format(Engine_Api::_()->getItemTable('review')->getAverageRating(array('owner'=>$owner)),1);
    
    // Get navigation
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_gutter');
    
  }

    
  
  
  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('review', null, 'create')->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('review_main');
    $this->view->form = $form = new Review_Form_Create();
    
    $to = $this->_getParam('to', null);
    $toValues = $this->_getParam('toValues', null);
    if ($toValues) $to = $toValues;
    if( $to !== null)
    {
      $toUser = Engine_Api::_()->user()->getUser($to);
      if(!$viewer->isBlockedBy($toUser)) {
        $this->view->toUser = $toUser;
        $form->toValues->setValue($to);
        $this->getRequest()->setPost('toValues', $toUser->getIdentity());
        //$form->removeElement('to');
      }
    }
    
    // If not post or form not valid, return
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $table = Engine_Api::_()->getItemTable('review');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
      	$featured = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'review', 'featured') ? 1 : 0;
      	
        // Create review
        $values = array_merge($form->getValues(), array(
          'user_id' => $form->toValues->getValue(),
          'owner_id' => $viewer->getIdentity(),
          'featured' => $featured,
        ));
        $values['recommend'] = $values['recommend'] ? 1 : 0;
        
        $review = $table->createRow();
        $review->setFromArray($values);
        $review->save();

        // Add tags
        $tags = preg_split('/[,]+/', $values['keywords']);
        $tags = array_filter(array_map("trim", $tags));
        $review->tags()->addTagMaps($viewer, $tags);

        $customfieldform = $form->getSubForm('customField');
        $customfieldform->setItem($review);
        $customfieldform->saveValues();

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;  
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
  
        $auth_keys = array(
         'view' => 'everyone',
         'comment' => 'registered',
        );
        
        foreach ($auth_keys as $auth_key => $auth_default)
        {
          $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
          $authMax = array_search($auth_value, $roles);
          
          foreach( $roles as $i => $role )
          {
            $auth->setAllowed($review, $role, $auth_key, ($i <= $authMax));
          }
        }

        
        // Add activity only if review is published
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $review, 'review_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $review);
        }

      	$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($review->getUser(), $viewer, $review, 'has_posted_review', array(
          'label' => $review->getShortType()
        ));
        
        
        // Commit
        $db->commit();
        
        return $this->_redirectCustom($review);

      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function editAction()
  {
    //if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $review = Engine_Api::_()->core()->getSubject('review');
    
    if( !$this->_helper->requireAuth()->setAuthParams($review, null, 'edit')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('review_main');

    $this->view->form = $form = new Review_Form_Edit(array(
      'item' => $review
    ));
    
    // only for create
    $form->removeElement('to');
    $form->removeElement('toValues');

    $form->populate($review->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
     'view' => 'everyone',
     'comment' => 'registered',
    );
    
    // Save review entry
    if( !$this->getRequest()->isPost() )
    {

      // prepare tags
      $reviewTags = $review->tags()->getTagMaps();
      
      $tagString = '';
      foreach( $reviewTags as $tagmap )
      {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->keywords->setValue($tagString);
      
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;
        
        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($review, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }
      
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }


    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['keywords']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $review->setFromArray($values);
      $review->modified_date = date('Y-m-d H:i:s');

      $review->tags()->setTagMaps($viewer, $tags);
      $review->save();

      // Save custom fields
      $customfieldform = $form->getSubForm('customField');
      $customfieldform->setItem($review);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();
      
      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
          
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($review, $role, $auth_key, ($i <= $authMax));
        }
      }
      
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  


  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $review = Engine_Api::_()->core()->getSubject('review');

    //if( $viewer->getIdentity() != $review->owner_id && !$this->_helper->requireAuth()->setAuthParams($review, null, 'edit')->isValid())
    if( !$this->_helper->requireAuth()->setAuthParams($review, null, 'delete')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');
    
    if( $this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true )
    {
      $this->view->review->delete();
      return $this->_helper->redirector->gotoRoute(array(), 'review_manage', true);
    }
  }

  public function tagsAction()
  {
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->form = $form = new Review_Form_Search();
               
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_quick'); 
    
    $this->view->tags = $tags = Engine_Api::_()->review()->getPopularTags(array('limit' => 999, 'order' => 'text'));
  }


  public function voteAction()
  {
    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $review = Engine_Api::_()->core()->getSubject('review');
    
    $helpful = $this->_getParam('helpful', 1) ? 1 : 0;
    
    if( !$review ) {
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This review does not seem to exist anymore.');
      return;
    }

    $vote = $review->votes()->getVote($viewer);
    
    if ($vote)
    {
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already voted on this review.');
      return;
    }
    
    
    $db = $review->votes()->getVoteTable()->getAdapter();
    $db->beginTransaction();
    try {
      $review->votes()->addVote($viewer, $helpful);

      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Thank you for your feedback!');
      
      $db->commit();
      
    } catch( Exception $e ) {
      $db->rollback();
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
      return;
    }

  }
  
  
  public function unvoteAction()
  {
    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->review = $review = Engine_Api::_()->core()->getSubject('review');
    
    $helpful = $this->_getParam('helpful', 1) ? 1 : 0;
    
    if( !$review ) {
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This review does not seem to exist anymore.');
      return;
    }

    $vote = $review->votes()->getVote($viewer);
    
    if (!$vote)
    {
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have not voted on this review.');
      return;
    }
    
    
    $db = $review->votes()->getVoteTable()->getAdapter();
    $db->beginTransaction();
    try {
      $review->votes()->removeVote($viewer);
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your feedback has been removed.');
      $db->commit();
      
    } catch( Exception $e ) {
      $db->rollback();
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_($e->getMessage());
      return;
    }

  }
  
}

