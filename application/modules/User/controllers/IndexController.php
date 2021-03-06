<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 8536 2011-03-01 04:43:10Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {

  }

  public function homeAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'home', true);
    }

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
  
  public function welcomeAction() {
  	$this->view->isWelcome = true;
  	return true;
  }

  public function browseAction()
  {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }
    if( !$this->_executeSearch() ) {
      throw new Exception('error');
    }

    if( $this->_getParam('ajax') ) {
      $this->renderScript('_browseUsers.tpl');
    }
  }
  
  protected function _executeSearch()
  {
    // Check form
    $form = new User_Form_Search(array(
      'type' => 'user'
    ));

    if( !$form->isValid($this->_getAllParams()) ) {
      $this->view->error = true;
      return false;
    }

    $this->view->form = $form;

    // Get search params
    $page = (int)  $this->_getParam('page', 1);
    $ajax = (bool) $this->_getParam('ajax', false);
    $options = $form->getValues();

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');
    
    //extract($options); // displayname
    $profile_type = @$options['profile_type'];
    $displayname = @$options['displayname'];
    extract($options['extra']); // is_online, has_photo, submit

    if( $this->_getParam('is_online', 0) ) $is_online = (int)$this->_getParam('is_online', 0) ;
    $most_followed = $this->_getParam('most_followed', 0);

    /*$select = new Zend_Db_Select($table->getAdapter());
    $select->from($userTableName, "*")
           ->joinLeft($membershipTableName, "`{$membershipTableName}`.`resource_id` = `{$userTableName}`.`user_id`", "COUNT({$membershipTableName}.resource_id) as count")
           ->where("{$userTableName}.search = ?", 1)
           ->where("{$userTableName}.enabled = ?", 1)
           ->group("{$membershipTableName}.resource_id")
           ->order("count DESC")
    ;
*/
    //$select1 = $table->getAdapter()->query($select)->fetchAll();


    // Contruct query
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
      ->where("{$userTableName}.search = ?", 1)
      ->where("{$userTableName}.enabled = ?", 1)
    ;

    // Build the photo and is online part of query
    if( !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    // Build the photo and is online part of query
    if( !empty($has_photo) ) {
      $select->where($userTableName.'.photo_id != ?', "0");
    }

    if( !empty($is_online) ) {
      $select
        ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
        ->group("engine4_user_online.user_id")
        ->where($userTableName.'.user_id != ?', "0");
    }

    if( $most_followed ) {
      $select
        ->joinLeft($membershipTableName, "`{$membershipTableName}`.`resource_id` = `{$userTableName}`.`user_id`", "COUNT({$userTableName}.user_id) as count")
        ->group("{$userTableName}.user_id")
        ->order("count DESC")
        ->order("{$membershipTableName}.resource_id DESC");
    }

    // Add displayname
    if( !empty($displayname) ) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach( $searchParts as $k => $v ) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    if( $this->_getParam('newest', 0) ) $select->order("{$userTableName}.creation_date DESC");
    elseif( !$most_followed ) $select->order("{$userTableName}.displayname ASC");
//echo "<pre>"; print_r($select->__toString()); echo "</pre>"; die();
    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(21);
    $paginator->setCurrentPageNumber($page);
    
    $this->view->page = $page;
    $this->view->ajax = $ajax;
    $this->view->users = $paginator;
    $this->view->totalUsers = $paginator->getTotalItemCount();
    $this->view->userCount = $paginator->getCurrentItemCount();
    $this->view->topLevelId = $form->getTopLevelId();
    $this->view->topLevelValue = $form->getTopLevelValue();
    $this->view->formValues = array_filter($options);

    return true;
  }
}
