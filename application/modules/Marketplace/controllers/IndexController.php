<?php

/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: IndexController.php 7250 2010-09-01 07:42:35Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_IndexController extends Core_Controller_Action_Standard {

    protected $_navigation;
    // true - sandbox, false - paypal original
    protected $_sandbox = false;

    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('marketplace', null, 'view')->isValid())
            return;
    }
	
    // NONE USER SPECIFIC METHODS
    public function indexAction() {
        $viewer = $this->_helper->api()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams('marketplace', null, 'view')->isValid())
            return;

        $this->view->navigation = $this->getNavigation();
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('marketplace', null, 'create')->checkRequire();

        $this->view->form = $form = new Marketplace_Form_Search();

        if (!$viewer->getIdentity()) {
            $form->removeElement('show');
        }
        $idd = $this->getRequest()->getParam('category');
        if (empty($idd))
            $id = 0; else
            $id = intval($this->getRequest()->getParam('category'));
		
        Engine_Api::_()->marketplace()->tree_list($id);

        $categ = implode(', ', Engine_Api::_()->marketplace()->_inarr);

        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($id)); //Engine_Api::_()->marketplace()->tree_list_load_subtree('0');

        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;
        // Populate form
        $this->view->categories = $categories = Engine_Api::_()->marketplace()->getCategories();

        Engine_Api::_()->marketplace()->tree_select($a_tree, '', 1);

        $newcategories = Engine_Api::_()->marketplace()->gettemp();
        foreach ($newcategories as $k => $e) {//$category )
            $form->category->addMultiOption($k, $e); //$category->category_id, $category->category_name);
        }

        // Process form
        if ($form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
        } else {
            $values = array();
        }

		$values['category'] = $categ;
        if ($this->getRequest()->isPost()) {
			$this->view->category = $values['category'] = $this->getRequest()->getPost('category');
		}

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids to pass to getMarketplacesPaginator
            $table = $this->_helper->api()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }

            $values['users'] = $ids;
        }

        // check to see if request is for specific user's listings
        $user_id = $this->_getParam('user');
        if ($user_id)
            $values['user_id'] = $user_id;


        $this->view->assign($values);

        // items needed to show what is being filtered in browse page
        if (!empty($values['tag']))
            $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        $archiveList = Engine_Api::_()->marketplace()->getArchiveList();
        $this->view->archive_list = $this->_handleArchiveList($archiveList);

        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        //$paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator($values, $customFieldValues);
        $paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator($values);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        if (!empty($values['category']))
            $this->view->categoryObject = Engine_Api::_()->marketplace()->getCategory($values['category']);

        if (!$this->getRequest()->isPost()) {
			$form->category->setValue($id);
		}
    }

    public function viewAction() {

        $paypal = $this->_helper->api()->getApi('paypal', 'marketplace');

        // $this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

        $viewer = $this->_helper->api()->user()->getViewer();
        $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));

        if (!$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'view')->isValid())
            return;

        $can_edit = $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams($marketplace, null, 'edit')->checkRequire();
        $this->view->allowed_upload = ( $viewer && $viewer->getIdentity()
                && Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'photo') );

        if ($marketplace) {
            $archiveList = Engine_Api::_()->marketplace()->getArchiveList($marketplace->owner_id);

            $this->view->owner = $owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
            $this->view->viewer = $viewer;

            $marketplace->view_count++;
            $marketplace->save();

            $this->view->marketplace = $marketplace;
            if ($marketplace->photo_id) {
                $this->view->main_photo = $marketplace->getPhoto($marketplace->photo_id);
            }

            // get archive list
            $this->view->archive_list = $this->_handleArchiveList($archiveList);

            // Load fields view helpers
            $view = $this->view;
            $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
            $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace);

            // album material
            $this->view->album = $album = $marketplace->getSingletonAlbum();
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $paginator->setItemCountPerPage(100);

            if ($marketplace->category_id != 0)
                $this->view->category = Engine_Api::_()->marketplace()->getCategory($marketplace->category_id);
            $this->view->userCategories = Engine_Api::_()->marketplace()->getUserCategories($this->view->marketplace->owner_id);
        }

        $this->view->paypalForm = $this->paypal()->form();
    }

    public function paypal() {
        $paypal = new Marketplace_Api_Payment($this->_sandbox);//true);
        $viewer = $this->_helper->api()->user()->getViewer();
        $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));
        if ($marketplace) {

            $this->view->owner = $owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
        }
        $user = $this->_helper->api()->user()->getUser($viewer->getIdentity());
        $paypal->setBusinessEmail($marketplace->business_email); //'owner_1293606948_biz@gmail.com');//"business@owner.com");
        $paypal->setPayer($user->email, $viewer->getIdentity()); //$user->user_id);
        $paypal->setAmount($marketplace->price); //"50");
        $paypal->setNumber($this->_getParam('marketplace_id') . ':' . $viewer->getIdentity());
        $paypal->addItem(array('item_name' => $marketplace->title . '(' . $marketplace->body . ')'));
        $paypal->setControllerUrl("http://" . $this->getRequest()->getHttpHost() . $this->view->url(array(), 'marketplace_extended', true) . '/payment'); //->url());
        return $paypal;
    }

    public function paymentAction() {
        $this->view->paypalForm = $this->paypal()->form();
    }

    public function paymentnotifyAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $paypal = new Marketplace_Api_Payment(true);
        $arrPost = $this->getRequest()->getPost();

        if ($paypal->validateNotify($arrPost)) {
            //xxx:yyy
            // xxx - marketplace_id, yyy - user_id;

            $order = explode(':', $arrPost['item_number']);

            $marketplace = Engine_Api::_()->getItem('marketplace', $order[0]);
            $values['user_id'] = $order[1];
            $values['owner_id'] = $marketplace->owner_id;
            $values['marketplace_id'] = $order[0];
            $values['count'] = 1;
            $values['summ'] = $arrPost['mc_gross'];
            $values['date'] = date('Y.m.d H:i:s');
            //$values['temp'] = $arrPost['payer_status'];
            $table = Engine_Api::_()->getDbtable('orders', 'marketplace');
            if ($marketplace->price == $arrPost['mc_gross'])
                $table->insert($values);
				
			$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
			$buyer = Engine_Api::_()->getItem('user', $order[1]);
			$marketplace_item = Engine_Api::_()->getItem('marketplace', $order[0]);
			
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$notifyApi->addNotification($owner, $buyer, $marketplace_item, 'marketplace_transaction_to_owner');
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$notifyApi->addNotification($buyer, $owner, $marketplace_item, 'marketplace_transaction_to_buyer');
        }
    }

    public function paymentreturnAction() {

        $viewer = $this->_helper->api()->user()->getViewer();
        $user = $this->_helper->api()->user()->getUser($viewer->getIdentity());
    }

    // USER SPECIFIC METHODS
    public function manageAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = $this->_helper->api()->user()->getViewer();

        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('marketplace', null, 'create')->checkRequire();
        $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'photo');

        $this->view->navigation = $this->getNavigation();
        $this->view->form = $form = new Marketplace_Form_Search();
        $form->removeElement('show');

        // Populate form
       // $a_tree = Engine_Api::_()->marketplace()->tree_list_load_subtree('0');
        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array(0));
        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;
        // Populate form
        $this->view->categories = $categories = Engine_Api::_()->marketplace()->getCategories();

        Engine_Api::_()->marketplace()->tree_select($a_tree, '', 1);

        $newcategories = Engine_Api::_()->marketplace()->gettemp();
        foreach ($newcategories as $k => $e) {
            $form->category->addMultiOption($k, $e);
        }

        // Process form
        $request = $this->getRequest()->getPost();

        // Process form
        if ($form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
        } else {
            $values = array();
        }

        $values['user_id'] = $viewer->getIdentity();

        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator($values, array()); //$price);//$customFieldValues);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        // maximum allowed marketplaces
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
    }

    public function listAction() {
        // Preload info
        $viewer = $this->_helper->api()->user()->getViewer();
        $this->view->owner = $owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));
        $archiveList = Engine_Api::_()->marketplace()->getArchiveList($owner->getIdentity());
        $this->view->archive_list = $this->_handleArchiveList($archiveList);

        $this->view->navigation = $this->getNavigation();

        // Make form
        $this->view->form = $form = new Marketplace_Form_Search();
        $form->removeElement('show');
        //$form->removeElement('browse-separator');

        // Populate form
        $this->view->categories = $categories = Engine_Api::_()->marketplace()->getCategories();
        foreach ($categories as $category) {
            $form->category->addMultiOption($category->category_id, $category->category_name);
        }

        // Process form
        $form->isValid($this->getRequest()->getPost());
        $values = $form->getValues();
        $values['user_id'] = $owner->getIdentity();
        $this->view->assign($values);

        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator($values);
        $paginator->setItemCountPerPage(10);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $this->view->userTags = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByTagger('marketplace', $owner);
        $this->view->userCategories = Engine_Api::_()->marketplace()->getUserCategories($owner->getIdentity());
    }

    public function createAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('marketplace', null, 'create')->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        //  $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));
        $this->view->navigation = $this->getNavigation();
        $this->view->form = $form = new Marketplace_Form_Create();

        // set up data needed to check quota
        $values['user_id'] = $viewer->getIdentity();
        $paginator = $this->_helper->api()->getApi('core', 'marketplace')->getMarketplacesPaginator($values);

        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();

        $marketplaceTable = Engine_Api::_()->getDbtable('marketplaces', 'marketplace');
        $db = $marketplaceTable->getAdapter();
        $db->beginTransaction();

        try {

            $mark = $marketplaceTable->getlastemail($viewer->getIdentity()); //sendInvites($viewer, $values['recipients'], @$values['message']);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            if (APPLICATION_ENV == 'development') {
                throw $e;
            }
        }

        $form->business_email->setValue($mark['business_email']);
        // If not post or form not valid, return
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getItemTable('marketplace');

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create marketplace
            $values = array_merge($form->getValues(), array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer->getIdentity(),
                    ));

            $marketplace = $table->createRow();
            $marketplace->setFromArray($values);
            $marketplace->save();

            // Set photo
            if (!empty($values['photo'])) {
                $marketplace->setPhoto($form->photo);
            }

            // Save custom fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($marketplace);
            $customfieldform->saveValues();

            // Set privacy
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = array("everyone");
            }
            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = array("everyone");
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($marketplace, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($marketplace, $role, 'comment', ($i <= $commentMax));
            }

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $marketplace, 'marketplace_new');
            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $marketplace);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect
        $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'photo');
        if ($allowed_upload) {
            return $this->_helper->redirector->gotoRoute(array('marketplace_id' => $marketplace->marketplace_id), 'marketplace_success', true);
        } else {
            return $this->_helper->redirector->gotoUrl($marketplace->getHref(), array('prependBase' => false));
        }
    }

    public function editAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
            
       //  $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($id));
        // $a_tree = Engine_Api::_()->marketplace()->tree_list_load_all();

       ///  print_r($a_tree);

        $viewer = $this->_helper->api()->user()->getViewer();
        $this->view->marketplace = $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));
        if (!Engine_Api::_()->core()->hasSubject('marketplace')) {
            Engine_Api::_()->core()->setSubject($marketplace);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Backup
        if ($viewer->getIdentity() != $marketplace->owner_id && !$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'edit')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Get navigation
        $navigation = $this->getNavigation(true);
        $this->view->navigation = $navigation;

        $this->view->form = $form = new Marketplace_Form_Edit(array(
                    'item' => $marketplace
                ));
        $form->removeElement('photo');

        $this->view->album = $album = $marketplace->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(100);

        foreach ($paginator as $photo) {
            $subform = new Marketplace_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
            $subform->removeElement('title');

            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
        }
        // Save marketplace entry
        $saved = $this->_getParam('done');

        if (!$this->getRequest()->isPost() || $saved) {

            if ($saved) {
                $url = $this->_helper->url->url(array('user_id' => $viewer->getIdentity(), 'marketplace_id' => $marketplace->getIdentity()), 'marketplace_entry_view');
                $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved. Click %s to view your listing.", '<a href="' . $url . '">here</a>');
                $form->addNotice($savedChangesNotice);
            }

			$marketplace->body = htmlspecialchars_decode($marketplace->body);
            // etc
            $form->populate($marketplace->toArray());
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
            foreach ($roles as $role) {
                if (1 === $auth->isAllowed($marketplace, $role, 'view')) {
                    $form->auth_view->setValue($role);
                }
                if (1 === $auth->isAllowed($marketplace, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
            }

            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        // handle save for tags
        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $marketplace->setFromArray($values);
            $marketplace->modified_date = date('Y-m-d H:i:s');

            $marketplace->save();

            $cover = $values['cover'];

            // Process
            foreach ($paginator as $photo) {
                $subform = $form->getSubForm($photo->getGuid());
                $subValues = $subform->getValues();
                $subValues = $subValues[$photo->getGuid()];
                unset($subValues['photo_id']);

                if (isset($cover) && $cover == $photo->photo_id) {
                    $marketplace->photo_id = $photo->file_id;
                    $marketplace->save();
                }

                if (isset($subValues['delete']) && $subValues['delete'] == '1') {
                    if ($marketplace->photo_id == $photo->file_id) {
                        $marketplace->photo_id = 0;
                        $marketplace->save();
                    }
                    $photo->delete();
                } else {
                    $photo->setFromArray($subValues);
                    $photo->save();
                }
            }

            // Save custom fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($marketplace);
            $customfieldform->saveValues();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
            if (!empty($values['auth_view'])) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = "everyone";
            }
            $viewMax = array_search($auth_view, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($marketplace, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
            if (!empty($values['auth_comment'])) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = "everyone";
            }
            $commentMax = array_search($auth_comment, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($marketplace, $role, 'comment', ($i <= $commentMax));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($marketplace) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_redirect("marketplaces/manage");
    }

    public function deleteAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $this->getNavigation();

        $viewer = $this->_helper->api()->user()->getViewer();
        $this->view->marketplace = $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));

        if ($viewer->getIdentity() != $marketplace->owner_id && !$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'delete')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $this->view->marketplace->delete();
            return $this->_redirect("marketplaces/manage");
        }
    }

    public function closeAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = $this->_helper->api()->user()->getViewer();
        $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));

        if ($viewer->getIdentity() != $marketplace->owner_id && !$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'edit')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $table = $marketplace->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $marketplace->closed = $this->_getParam('closed');
            $marketplace->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_redirect("marketplaces/manage");
    }

    public function successAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $this->getNavigation();

        $viewer = $this->_helper->api()->user()->getViewer();
        $this->view->marketplace = $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));

        if ($viewer->getIdentity() != $marketplace->owner_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            return $this->_redirect("marketplaces/photo/upload/subject/marketplace_" . $this->_getParam('marketplace_id'));
        }

        print_r($this->getRequest()->isPost());
    }

    // Utility

    public function getNavigation($active = false) {


        //$db->
        if (is_null($this->_navigation)) {
            $navigation = $this->_navigation = new Zend_Navigation();

            if ($this->_helper->api()->user()->getViewer()->getIdentity()) {
                $navigation->addPage(array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('Browse Listings'),
                    'route' => 'marketplace_browse',
                    'module' => 'marketplace',
                    'controller' => 'index',
                    'action' => 'index'
                ));

                $navigation->addPage(array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('My Listings'),
                    'route' => 'marketplace_manage',
                    'module' => 'marketplace',
                    'controller' => 'index',
                    'action' => 'manage',
                    'active' => $active
                ));
                if ($this->_helper->requireAuth()->setAuthParams('marketplace', null, 'create')->checkRequire()) {
                    $navigation->addPage(array(
                        'label' => Zend_Registry::get('Zend_Translate')->_('Post a New Listing'),
                        'route' => 'marketplace_create',
                        'module' => 'marketplace',
                        'controller' => 'index',
                        'action' => 'create'
                    ));
                }
                $ordersTable = Engine_Api::_()->getDbtable('orders', 'marketplace');

                $count = $ordersTable->getCountByUser($this->_helper->api()->user()->getViewer()->getIdentity());

                if ($count > 0)
                    $navigation->addPage(array(
                        'label' => Zend_Registry::get('Zend_Translate')->_('Reports'),
                        'route' => 'marketplace_reports',
                        'module' => 'marketplace',
                        'controller' => 'index',
                        'action' => 'reports'
                    ));
            }
        }
        return $this->_navigation;
    }

    protected function _handleArchiveList($results) {
        $localeObject = Zend_Registry::get('Locale');

        $marketplace_dates = array();
        foreach ($results as $result)
            $marketplace_dates[] = strtotime($result->creation_date);

        // GEN ARCHIVE LIST
        $time = time();
        $archive_list = array();

        foreach ($marketplace_dates as $marketplace_date) {
            $ltime = localtime($marketplace_date, TRUE);
            $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
            $ltime["tm_year"] = $ltime["tm_year"] + 1900;

            // LESS THAN A YEAR AGO - MONTHS
            if (false && $marketplace_date + 31536000 > $time) {
                $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
                $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
                //$label = date('F Y', $marketplace_date);
                $type = 'month';

                $marketplaceDateObject = new Zend_Date($marketplace_date);
                $format = $localeObject->getTranslation('MMMMd', 'dateitem', $localeObject);
                $label = $marketplaceDateObject->toString($format);
            }

            // MORE THAN A YEAR AGO - YEARS
            else {
                $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
                $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);

                $type = 'year';

                $marketplaceDateObject = new Zend_Date($marketplace_date);
                $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
                if (!$format) {
                    $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
                }
                $label = $marketplaceDateObject->toString($format);
            }

            if (!isset($archive_list[$date_start])) {
                $archive_list[$date_start] = array(
                    'type' => $type,
                    'label' => $label,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'count' => 1
                );
            } else {
                $archive_list[$date_start]['count']++;
            }
        }

        return $archive_list;
    }

    public function reportsAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $this->getNavigation();

        $page = $this->_getParam('page', 1);

        $this->view->formFilter = $formFilter = new Marketplace_Form_Filter();

        $viewer = $this->_helper->api()->user()->getViewer();
        $table = $this->_helper->api()->getDbtable('orders', 'marketplace');
        $select = $table->select();

        // Process form
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
                    'order' => 'order_id',
                    'order_direction' => 'DESC',
                        ), $values);

        $this->view->assign($values);

        // Make paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    }

}

