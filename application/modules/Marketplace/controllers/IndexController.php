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
class Marketplace_IndexController extends Core_Controller_Action_Standard 
{
  protected $_navigation;
  // true - sandbox, false - paypal original
  protected $_sandbox = true;
  //protected $_sandbox = false;

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
      $usersTable = Engine_Api::_()->getDbTable('users', 'user');
      
      $idd = $this->getRequest()->getParam('category');
      if (empty($idd))
          $id = 0; else
          $id = intval($this->getRequest()->getParam('category'));
	
      Engine_Api::_()->marketplace()->tree_list($id);

      $categ = implode(', ', Engine_Api::_()->marketplace()->_inarr);

      $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($id));

      $this->view->a_tree = $a_tree;
      $this->view->urls = $this->_helper->url;
      // Populate form
      $this->view->categories = $categories = Engine_Api::_()->marketplace()->getCategories();

      Engine_Api::_()->marketplace()->tree_select($a_tree, '', 1);

      $newcategories = Engine_Api::_()->marketplace()->gettemp();
      foreach ($newcategories as $category_id => $category_title) {
          $form->category->addMultiOption($category_id, $category_title);
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
          $select = $viewer->membership()->getMembersSelect('user_id');
          $friends = $usersTable->fetchAll($select);
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

      $values['closed'] = 0;
      $values['page'] = $this->_getParam('page', 1);
      $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.page', 20);

      $this->view->paginator = $paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator($values);

      if (!empty($values['category']))
          $this->view->categoryObject = Engine_Api::_()->marketplace()->getCategory($values['category']);

      if (!$this->getRequest()->isPost()) {
		    $form->category->setValue($id);
	    }

      $this->view->loginForm = new User_Form_Login();

      $userList = $usersTable->select()->where('photo_id > 0')->order('creation_date DESC')->limit(8)->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      $this->view->totalUsers = $usersTable->select()->from($usersTable->info("name"), "count(*) as total")->query()->fetchColumn();
      $this->view->userList = Engine_Api::_()->getItemMulti('user', $userList);
  }

  public function ajaxlistAction() {
      $viewer = $this->_helper->api()->user()->getViewer();

      if (!$this->_helper->requireAuth()->setAuthParams('marketplace', null, 'view')->isValid()) eturn;

      $this->view->form = $form = new Marketplace_Form_Search();
      // Process form
      if ($form->isValid($this->getRequest()->getPost())) {
          $values = $form->getValues();
      } else {
          $values = array();
      }

      // check to see if request is for specific user's listings
      $user_id = $this->_getParam('user');
      if ($user_id) $values['user_id'] = $user_id;
      $values['page'] = $this->_getParam('page', 2);
      $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.page', 20);
      $values['closed'] = 0;
      
      $this->view->paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator( $values );

      if( ($values['page'] - 1) * $values['limit'] >= $this->view->paginator->getTotalItemCount() ) die();

      $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

      $this->view->loginForm = new User_Form_Login();
      $this->_helper->layout->setLayout('empty');
  }




  public function viewAction() 
  {
	    $viewer = Engine_Api::_()->user()->getViewer();
      $paypal = $this->_helper->api()->getApi('paypal', 'marketplace');
      $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));
	
	    if(empty($marketplace)) return;

      $this->view->loginForm = new User_Form_Login();

      $this->view->discount_sum = $discount_sum = 0;

	    if(Engine_Api::_()->marketplace()->couponIsActive()) :

		    $couponTable = Engine_Api::_()->getDbTable('coupons', 'marketplace');
		    $couponTableName = $couponTable->info('name');
		    $couponcartTable = Engine_Api::_()->getDbTable('couponcarts', 'marketplace');
		    $couponcartTableName = $couponcartTable->info('name');
		
		    $postValues = $this->getRequest()->getPost();
		    if($postValues && !empty($postValues['coupon_code'])){
			    $coupon_code = $this->_getParam('coupon_code', '');
			    if($coupon_code){
				    $coupon_select = $couponTable->select()
					    ->where('code = ?', $coupon_code)
					    ->where('user_id = ?', $marketplace->getOwner()->getIdentity())
				    ;
				    $coupon_res = $couponTable->fetchRow($coupon_select);
				    if($coupon_res->coupon_id && $coupon_res->user_id == $marketplace->getOwner()->getIdentity()){
					    $couponcartTable->delete(
						    array('user_id' => $viewer->getIdentity())
					    );
					    $couponcartTable->insert(array(
						    'coupon_id' => $coupon_res->coupon_id,
						    'user_id' => $viewer->getIdentity()
					    ));
					    $this->view->coupon_error = 2;
				    }else{
					    $this->view->coupon_error = 1;
				    }
			    }
		    }

		    $coupon_select = $couponTable->getAdapter()
			    ->select()
			    ->from($couponcartTableName)
			    ->joinLeft($couponTableName, "`{$couponTableName}`.coupon_id = `{$couponcartTableName}`.coupon_id")
			    ->where("{$couponcartTableName}.user_id = ?", $viewer->getIdentity())
			    ->where("{$couponTableName}.user_id = ?", $marketplace->getOwner()->getIdentity())
		    ;
		    $this->view->coupon_res = $coupon_res = $couponTable->getAdapter()->fetchRow($coupon_select);
		    if($coupon_res) {
			    $this->view->discount = intval($coupon_res['percent']);
		    } else {
			    $this->view->discount = 0;
		    }

		    if($this->view->discount){
			    $this->view->discount_sum = $discount_sum = $marketplace->price * ($this->view->discount / 100);
		    }

	    endif;
	
      //if (!$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'view')->isValid()) return;

      $can_edit = $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams($marketplace, null, 'edit')->checkRequire();
      $this->view->allowed_upload = ( $viewer && $viewer->getIdentity()
              && Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'marketplace', 'photo') );

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
      $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace);

      // only category profile question
      $categoryTree = Engine_Api::_()->marketplace()->tree_list_load_path($marketplace->category_id); 
      $mainParentCategory = !empty($categoryTree) ? $categoryTree[0]['k_item'] : $marketplace->category_id;

      /////////////////////////////////////////

      $this->view->mycatid = $id = $marketplace->category_id;    
      Engine_Api::_()->marketplace()->tree_list($id);
      //$categ2 = implode(', ', Engine_Api::_()->marketplace()->_inarr);
      $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($id));
      $this->view->a_tree = $a_tree;
      $this->view->urls = $this->_helper->url;	

      /////////////////////////////////////////

      $deletedElemets = array();
      foreach($fieldStructure as $map) {
          $field = $map->getChild();
          if( $field->category_id != $mainParentCategory) {
            $deletedElemets[] = $map->field_id . "_" . $map->option_id . "_" . $map->child_id;
          }
      }
      foreach($deletedElemets as $name) {
          unset($fieldStructure[$name]);
      }

      $this->view->fieldStructure = $fieldStructure;

      // album material
      $this->view->album = $album = $marketplace->getSingletonAlbum();
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $paginator->setItemCountPerPage(100);

      if ($marketplace->category_id != 0)
          $this->view->category = Engine_Api::_()->marketplace()->getCategory($marketplace->category_id);
      $this->view->userCategories = Engine_Api::_()->marketplace()->getUserCategories($this->view->marketplace->owner_id);

	    //$this->view->product_shipping_fee = $product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $viewer->getIdentity());
      $this->view->product_shipping_fee = $product_shipping_fee = $marketplace->shipping;
      $this->view->inspection_fee = $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);
	    $this->view->total_amount = $total_amount = floatval($marketplace->price - $discount_sum + $product_shipping_fee);
	
      //$this->view->prepay = false;

	    if(Engine_Api::_()->marketplace()->authorizeIsActive()) {
		    $authorize_login = ($marketplace->authorize_login?$marketplace->authorize_login:'7sBYqTp344eh');
		    $authorize_key = ($marketplace->authorize_key?$marketplace->authorize_key:'8sY8eUVj47M46dxA');
		    $testmode = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.authorize.testmode', '0');
		    $myAuthorize = new Marketplaceauthorize_Api_Authorize();
		    $myAuthorize->setUserInfo($authorize_login, $authorize_key);
		    $myAuthorize->addField('x_Receipt_Link_URL', 'http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl().'/marketplaces/paymentreturn/payment/authorize');
		    $myAuthorize->addField('x_Relay_URL', 'http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl().'/marketplaces/paymentnotify/payment/authorize');
		    $myAuthorize->addField('x_Description', $marketplace->getTitle());
		    $myAuthorize->addField('x_Amount', $total_amount);
		    $myAuthorize->addField('x_Invoice_num', $marketplace->getIdentity().':'.$viewer->getIdentity());
		    $myAuthorize->addField('x_product_id', $marketplace->getIdentity());
		    $myAuthorize->addField('x_user_id', $viewer->getIdentity());
		    $myAuthorize->enableTestMode();
		    $this->view->paymentForm = $myAuthorize->render();
      } else {
        if( $viewer->getIdentity() ) {
	        $this->view->paymentForm = $this->paypal(array('discount_sum' => $discount_sum))->form();
        }
        /* else {
          $request = $this->getRequest()->getPost();
          if( isset($request['marketplaces_email']) and !empty($request['marketplaces_email']) ) {
            $this->view->paymentForm = $this->paypal(array('anonymous_purchase' => $request['marketplaces_email']))->form();
          }
          else {
            $this->view->paymentForm = new Marketplace_Form_Prepaypal();
            $this->view->prepay = true;
          }
        }
        */
      }

      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      $this->view->likeList = $likesTable->getLikePaginator($marketplace, 5);
	
	    if(Engine_Api::_()->marketplace()->cartIsActive()){
		    $this->view->already_in_cart = array();
    		$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
		    if($viewer->getIdentity()){
			    $cart_select = $cartTable->select()
				    ->where('user_id = ?', $viewer->getIdentity())
				    ->where('marketplace_id = ?', $marketplace->getIdentity())
			    ;
			    $this->view->already_in_cart = $cartTable->fetchRow($cart_select);
		    }
        else {
          $this->view->already_in_cart = $cartTable->productIsAlreadyInCookieCart($marketplace->getIdentity());
        }
	    }
  }

  public function authorize($params = array()) {
	}
	
  public function paypal($params = array()) {
		$discount_sum = floatval(($params['discount_sum']?$params['discount_sum']:0));
    $anonymous_purchase = (isset($params['anonymous_purchase']) and !empty($params['anonymous_purchase']) )? $params['anonymous_purchase'] : null;

    $paypal = new Marketplace_Api_Payment($this->_sandbox);//true);
    $viewer = $this->_helper->api()->user()->getViewer();

    $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));
    if ($marketplace) {
        $this->view->owner = $owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
    }
    //$paypal->setBusinessEmail($marketplace->business_email);
    $paypal->setBusinessEmail( Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.mainpaypal', $marketplace->business_email) );

    if( !$anonymous_purchase ) {
      $userID = $viewer->getIdentity();
      $paypal->setPayer($viewer->email, $viewer->getIdentity());
    }
    else {
      $userID = 0;
      $paypal->setPayer($anonymous_purchase, $noUser);
      $paypal->setCustom($anonymous_purchase);
    }

		//$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $viewer->getIdentity());
    $product_shipping_fee = $marketplace->shipping;
		$final_amount = floatval($marketplace->price - $discount_sum + $product_shipping_fee);
		if($final_amount < 0){
			$final_amount = 0;
		}
        $paypal->setAmount($final_amount); //"50");
        $paypal->setNumber($this->_getParam('marketplace_id') . ':' . $userID);
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
		if(Engine_Api::_()->marketplace()->cartIsActive()){
			$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
		}
		if(Engine_Api::_()->marketplace()->couponIsActive()){
			$couponcartTable = Engine_Api::_()->getDbTable('couponcarts', 'marketplace');
		}
		if($this->_getParam('payment') == 'authorize'){
			$myAuthorize_order = $_POST;
			if(strstr($myAuthorize_order['x_product_id'], '-')){//few items
				$order = explode(':', $myAuthorize_order['x_product_id']);
				if(strstr($order, '|')){
					$item_ids = explode('|', $order[0]);
					$first_item_id = $item_ids[0];
				}else{
					$first_item_id = $order[0];
				}
				$item_id_info = explode('-', $first_item_id);
				$item_id = $item_id_info[0];
				$first_marketplace = Engine_Api::_()->getItem('marketplace', $item_id);
			}else{
				$first_marketplace = Engine_Api::_()->getItem('marketplace', $order['x_product_id']);
			}
			ob_start();
			print_r($first_marketplace->toArray());
			$c = ob_get_clean();
			file_put_contents(APPLICATION_PATH . '/temporary/log/post.log', $c, FILE_APPEND);
			$authorize_login = ($first_marketplace->authorize_login?$first_marketplace->authorize_login:'7sBYqTp344eh');
			$authorize_key = ($first_marketplace->authorize_key?$first_marketplace->authorize_key:'8sY8eUVj47M46dxA');
			$testmode = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.authorize.testmode', '0');
			$myAuthorize = new Marketplaceauthorize_Api_Authorize();
			$myAuthorize->ipnLog = TRUE;
			$myAuthorize->setUserInfo($authorize_login, $authorize_key);
			$myAuthorize->enableTestMode();
			if ($myAuthorize->validateIpn())
			{
				ob_start();
				print_r($myAuthorize->ipnData);
				$c = 'SUCCESS' . "\n" . ob_get_clean();
				file_put_contents(APPLICATION_PATH . '/temporary/log/authorize.log', $c);
				$myAuthorize_order = $myAuthorize->ipnData;
				
				if(strstr($myAuthorize_order['x_product_id'], '-')){//few items
					$order = explode(':', $myAuthorize_order['x_product_id']);
					$final_amount = 0;
					$item_ids = explode('|', $order[0]);
					$values = array();
					$title_arr = array();
					$ids_arr = array();
					foreach($item_ids as $key => $item_count_id){
						
						$item_id_info = explode('-', $item_count_id);
						$item_id = $item_id_info[0];
						$item_count = $item_id_info[1];
						
						$marketplace = Engine_Api::_()->getItem('marketplace', $item_id);
						if(empty($marketplace))
							return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'result' => 'error'), 'marketplace_paymentreturn', true);
						$title_arr[] = $marketplace->title;
						$ids_arr[] = $item_id;
							
						$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
						$buyer = Engine_Api::_()->getItem('user', $myAuthorize_order['x_user_id']);
						
						////////////////////////////////////////////////////////////discount
						$coupon_discount = Engine_Api::_()->marketplace()->getDiscount($marketplace->marketplace_id,  $myAuthorize_order['x_user_id']);
						//$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $myAuthorize_order['x_user_id']);
            $product_shipping_fee = $marketplace->shipping;
						$final_amount += ($marketplace->price + $product_shipping_fee - $coupon_discount) * $item_count;
						////////////////////////////////////////////////////////////discount
					}
					if ($final_amount == $myAuthorize_order['x_amount']){
						foreach($item_ids as $key => $item_count_id){
							
							$item_id_info = explode('-', $item_count_id);
							$item_id = $item_id_info[0];
							$item_count = $item_id_info[1];
							
							$marketplace = Engine_Api::_()->getItem('marketplace', $item_id);
							$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
							$buyer = Engine_Api::_()->getItem('user', $myAuthorize_order['x_user_id']);
							////////////////////////////////////////////////////////////discount
							$coupon_discount = Engine_Api::_()->marketplace()->getDiscount($marketplace->marketplace_id,  $myAuthorize_order['x_user_id']);
							//$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $myAuthorize_order['x_user_id']);
              $product_shipping_fee = $marketplace->shipping;
							////////////////////////////////////////////////////////////discount
							
							$values['user_id'] = $myAuthorize_order['x_user_id'];
							$values['owner_id'] = $marketplace->owner_id;
							$values['marketplace_id'] = $item_id;
							$values['count'] = $item_count;
							$values['summ'] = $marketplace->price + $product_shipping_fee - $coupon_discount;
							$values['date'] = date('Y-m-d H:i:s');
						
							$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
							$notifyApi->addNotification($owner, $buyer, $marketplace, 'marketplace_transaction_to_owner');
							$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
							$notifyApi->addNotification($buyer, $owner, $marketplace, 'marketplace_transaction_to_buyer');
							
							$table = Engine_Api::_()->getDbtable('orders', 'marketplace');
							$table->insert($values);
							if(Engine_Api::_()->marketplace()->cartIsActive()){
								$cartTable->delete(array(
									'user_id = ?' => $myAuthorize_order['x_user_id'],
									'marketplace_id = ?' => $marketplace->getIdentity(),
								));
							}
							if(Engine_Api::_()->marketplace()->couponIsActive()){
								$couponcartTable->delete(array(
									'user_id = ?' => $myAuthorize_order['x_user_id']
								));
							}
						}
					}
				}elseif(!empty($order['x_product_id'])){//one item
					$marketplace = Engine_Api::_()->getItem('marketplace', $order['x_product_id']);

					if(empty($marketplace))
						return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'result' => 'error'), 'marketplace_paymentreturn', true);
					////////////////////////////////////////////////////////////discount
					$coupon_discount = Engine_Api::_()->marketplace()->getDiscount($cartitem->marketplace_id, $order[1]);
					//$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $order[1]);
          $product_shipping_fee = $marketplace->shipping;
					$final_amount = $marketplace->price + $product_shipping_fee - $coupon_discount;
					////////////////////////////////////////////////////////////discount

					$values['user_id'] = $order['x_user_id'];
					$values['owner_id'] = $marketplace->getOwner()->getIdentity();
					$values['marketplace_id'] = $order['x_product_id'];
					$values['count'] = 1;
					$values['summ'] = $order['x_amount'];
					$values['date'] = date('Y-m-d H:i:s');
					$table = Engine_Api::_()->getDbtable('orders', 'marketplace');
					if ($final_amount == $order['x_amount'])
						$table->insert($values);
						
					$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
					$buyer = Engine_Api::_()->getItem('user', $order['x_user_id']);
					
					$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
					$notifyApi->addNotification($owner, $buyer, $marketplace, 'marketplace_transaction_to_owner');
					$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
					$notifyApi->addNotification($buyer, $owner, $marketplace, 'marketplace_transaction_to_buyer');
					if(Engine_Api::_()->marketplace()->couponIsActive()){
						$couponcartTable->delete(array(
							'user_id = ?' => $order['x_user_id']
						));
					}
				}
			}
			else
			{
				ob_start();
				print_r($myAuthorize->ipnData);
				$c = 'FAILURE' . "\n" . ob_get_clean();
				file_put_contents(APPLICATION_PATH . '/temporary/log/authorize.log', $c);
			}
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'marketplace_paymentreturn', true);
		}else{

			$paypal = new Marketplace_Api_Payment(true);
			$arrPost = $this->getRequest()->getPost();

			if ($paypal->validateNotify($arrPost)) {

				$order = explode(':', $arrPost['item_number']);
				$user_id = (int)$order[1];
				if(strstr($order[0], '-')){//few items
					$final_amount = 0;
					$item_ids = explode('|', $order[0]);

					$values = array();
					$title_arr = array();
					$ids_arr = array();
					foreach($item_ids as $key => $item_count_id){
						
						$item_id_info = explode('-', $item_count_id);
						$item_id = $item_id_info[0];
            $item_count = $item_id_info[1];

						$marketplace = Engine_Api::_()->getItem('marketplace', $item_id);
						if(empty($marketplace) or (!$user_id and empty($arrPost['payer_email']) ))
							return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'result' => 'error'), 'marketplace_paymentreturn', true);

            /*if( preg_match("/\d(?=\+)/i", $item_count, $m) ) {
						     $item_count = $m[0];
                 $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);
            }*/
            $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);

						$title_arr[] = $marketplace->title;
						$ids_arr[] = $item_id;
							
						$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);

  			    //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $user_id);						
            $product_shipping_fee = $marketplace->shipping;

            if( $user_id ) {
						    ////////////////////////////////////////////////////////////discount
						    $coupon_discount = Engine_Api::_()->marketplace()->getDiscount($marketplace->marketplace_id,  $user_id);
						    ////////////////////////////////////////////////////////////discount
            }
            $final_amount += ($marketplace->price + $product_shipping_fee + $inspection_fee - $coupon_discount) * $item_count;
					}

/*ob_start();
print_r($arrPost);
print_r($final_amount . " - " . $arrPost['mc_gross']);
$c = ob_get_clean();
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/fashbay/temporary/log/pplog.txt', $c );
chmod($_SERVER['DOCUMENT_ROOT'] . '/fashbay/temporary/log/pplog.txt', 0777);
*/


					if ((string)$final_amount == (string)$arrPost['mc_gross']){

            $shippinginfoTable = Engine_Api::_()->getDbtable('shippinginfo', 'marketplace');
            $shippingInfo = $shippinginfoTable->select()->where("user_id = {$user_id} and paid = 0 ")->query()->fetchColumn();
            if( $shippingInfo ) $shippinginfoTable->update(array('paid' => '1'), "user_id = {$user_id}");

						foreach($item_ids as $key => $item_count_id){
							
							$item_id_info = explode('-', $item_count_id);
							$item_id = $item_id_info[0];
							$item_count = $item_id_info[1];

							$marketplace = Engine_Api::_()->getItem('marketplace', $item_id);

              /*if( preg_match("/\d(?=\+)/i", $item_count, $m) ) {
						     $item_count = $m[0];
                 $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);
              } else {
                 $inspection_fee = 0;
              }*/
              $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);

							$owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);
							
              //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $user_id);
              $product_shipping_fee = $marketplace->shipping;

              if( $user_id ) {
							  ////////////////////////////////////////////////////////////discount
							  $coupon_discount = Engine_Api::_()->marketplace()->getDiscount($marketplace->marketplace_id,  $user_id);
							  ////////////////////////////////////////////////////////////discount
              }
							
							$values['user_id'] = $user_id;
							$values['owner_id'] = $marketplace->owner_id;
							$values['marketplace_id'] = $item_id;
							$values['count'] = $item_count;
							$values['summ'] = $marketplace->price + $product_shipping_fee + $inspection_fee - $coupon_discount;
              $values['price'] = $marketplace->price;
              $values['shipping'] = $product_shipping_fee;
              $values['inspection'] = $inspection_fee;
							$values['date'] = date('Y-m-d H:i:s');
              $values['contact_email'] = $arrPost['payer_email'];
              $values['shipping_info'] = $shippingInfo;

              $table = Engine_Api::_()->getDbtable('orders', 'marketplace');
 					    $table->insert($values);

						
              if( $user_id ) {
                  $buyer = Engine_Api::_()->getItem('user', $user_id);
							    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
							    $notifyApi->addNotification($owner, $buyer, $marketplace, 'marketplace_transaction_to_owner');
							    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
							    $notifyApi->addNotification($buyer, $owner, $marketplace, 'marketplace_transaction_to_buyer');
							
							    if(Engine_Api::_()->marketplace()->cartIsActive()){
								    $cartTable->delete(array(
									    'user_id = ?' => $user_id,
									    'marketplace_id = ?' => $marketplace->getIdentity(),
								    ));
							    }
							    if(Engine_Api::_()->marketplace()->couponIsActive()){
								    $couponcartTable->delete(array(
									    'user_id = ?' => $user_id
								    ));
							    }
              } else { //mailing
                  if( $owner->getIdentity() and $marketplace->getIdentity() ) {
                     $this->_paymentMailing($values['contact_email'], $owner, $marketplace, $values['count']);
                  }
              }
						}
					}
					
				} else { //one item
          //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($marketplace->getIdentity(), $user_id);
          $product_shipping_fee = $marketplace->shipping;

          if( $user_id ) {
  					////////////////////////////////////////////////////////////discount
	  				$coupon_discount = Engine_Api::_()->marketplace()->getDiscount($cartitem->marketplace_id, $user_id);
	  				////////////////////////////////////////////////////////////discount
          }

					$marketplace = Engine_Api::_()->getItem('marketplace', $order[0]);
					if(empty($marketplace) or (!$user_id and empty($arrPost['payer_email']) ))
						return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'result' => 'error'), 'marketplace_paymentreturn', true);

          //if( preg_match("/\d(?=\+)/i", $item_count, $m) ) {
				     $item_count = $m[0];
             $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($marketplace->price);
          //} else {
          //   $inspection_fee = 0;
          //}

					$values['user_id'] = $user_id;
					$values['owner_id'] = $marketplace->owner_id;
					$values['marketplace_id'] = $order[0];
					$values['count'] = 1;
					$values['summ'] = $arrPost['mc_gross'];
          $values['price'] = $marketplace->price;
          $values['shipping'] = $product_shipping_fee;
          $values['inspection'] = $inspection_fee;
					$values['date'] = date('Y-m-d H:i:s');
          $values['contact_email'] = $arrPost['payer_email'];
					$table = Engine_Api::_()->getDbtable('orders', 'marketplace');
					$final_amount = $marketplace->price + $product_shipping_fee + $inspection_fee - $coupon_discount;

					if ($final_amount == $arrPost['mc_gross'])
						$table->insert($values);
						
          $owner = Engine_Api::_()->getItem('user', $marketplace->owner_id);

          if( $user_id != 0) { 
    					$buyer = Engine_Api::_()->getItem('user', $user_id);
          
					    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
					    $notifyApi->addNotification($owner, $buyer, $marketplace, 'marketplace_transaction_to_owner');
					    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
					    $notifyApi->addNotification($buyer, $owner, $marketplace, 'marketplace_transaction_to_buyer');
					    if(Engine_Api::_()->marketplace()->couponIsActive()){
						    $couponcartTable->delete(array(
							    'user_id = ?' => $user_id
						    ));
					    }
          } else { //mailing
              if( $owner->getIdentity() and $marketplace->getIdentity() ) {
                  $this->_paymentMailing($values['contact_email'], $owner, $marketplace, $values['count']);
              }
          } // user_id != 0
				}
			}
		}
    }

    private function _paymentMailing($buyerEmail, $owner, $marketplace, $count) {

      $adminAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'admin@' . $_SERVER['HTTP_HOST']);
      $adminName = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.name', 'Site Admin');

      $toOwnerAddress = $owner->email;

      $toOwnerName = $owner->username;
      $toBuyerName = $this->view->translate('Unregistered user');

      $subjectTemplate = Zend_Registry::get('Zend_Translate')->_("New Order");

      $bodyAdmin = $this->view->translate('<strong>%1$s</strong> bought the "%2$s" from %3$s. Count: %4$s', $buyerEmail, $marketplace->getTitle(), $owner->username, $count);
      $bodyOwner = $this->view->translate('<strong>%1$s</strong> bought the "%2$s". Count: %3$s', $buyerEmail, $marketplace->getTitle(), $count);
      $bodyBuyer = $this->view->translate('You bought the "%1$s" from %2$s. Count: %3$s', $marketplace->getTitle(), $owner->username, $count);
      $bodyBuyer .= " ".$this->view->translate('Payment done, please proceed with delivery. Owner email: %1$s', $owner->email);

      // mail to admin
      $mail = Engine_Api::_()->getApi('mail', 'core')->create()
              ->addTo($adminAddress, $adminName)
              ->setFrom($adminAddress, $adminName)
              ->setSubject($subjectTemplate)
              ->setBodyHtml($bodyAdmin)
      ;
      Engine_Api::_()->getApi('mail', 'core')->sendRaw($mail);

      // mail to owner
      $mail = Engine_Api::_()->getApi('mail', 'core')->create()
              ->addTo($toOwnerAddress, $toOwnerName)
              ->setFrom($adminAddress, $adminName)
              ->setSubject($subjectTemplate)
              ->setBodyHtml($bodyOwner)
      ;
      Engine_Api::_()->getApi('mail', 'core')->sendRaw($mail);

      // mail to buyer
      $mail = Engine_Api::_()->getApi('mail', 'core')->create()
              ->addTo($buyerEmail, $toBuyerName)
              ->setFrom($adminAddress, $adminName)
              ->setSubject($subjectTemplate)
              ->setBodyHtml($bodyBuyer)
      ;
      Engine_Api::_()->getApi('mail', 'core')->sendRaw($mail);
    }

    public function paymentreturnAction() {

        $viewer = $this->_helper->api()->user()->getViewer();
		    $this->view->result = $this->_getParam('result', 'success');
		
        $mids = $this->_getParam('mids');
        $mids_ids = explode('_', $mids);

		    if(Engine_Api::_()->marketplace()->cartIsActive()){
			    $cartTable  = Engine_Api::_()->getDbTable('cart', 'marketplace');

          if( $viewer and $viewer->getIdentity()) {
			        $select = $cartTable->select()
				        ->where('user_id = ?', $viewer->getIdentity())
				        ->limit(1);
			        $this->view->cartContent = $cartTable->fetchRow($select);

          } else {
              $cartTable->deleteCookieCartItemArray( $mids_ids );
              $this->view->cartContent = $cartTable->getCookieCart();
          }
		    } 
        else $this->view->cartContent = array();

        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookApi = $facebookTable->getApi();

        $this->view->fbconnect = $fbconnect = ($facebookApi and $facebookApi->getUser()) ? true : false;

        if( $request = $this->getRequest()->getPost() ) {

          if( isset($request['fbconnect']) ) header("Location:{$facebook->getLoginUrl(array('req_perms' => 'email,offline_access', 'scope' => 'publish_stream'))}");
          if( isset($request['fbpost']) ) :
              if( empty($mids_ids) ) return $this->_helper->redirector->gotoRoute(array(), 'marketplace_extended');
              $marketplace = Engine_Api::_()->getItem('marketplace', (int)$mids_ids[0]);
              if( !$marketplace ) return $this->_helper->redirector->gotoRoute(array(), 'marketplace_extended');


              try {
                //$fb_uid = $facebookTable->find($viewer->getIdentity())->current();
                if( $fbconnect ) {
                    //$fb_uid and $fb_uid->facebook_uid and $fbconnect and $facebookApi->getUser() == $fb_uid->facebook_uid ) {

                      $url    = 'http://' . $_SERVER['HTTP_HOST'] . $marketplace->getHref();
                      $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $marketplace->getPhotoUrl('thumb.icon');
                      $name   = $marketplace->getTitle();
                      $desc   = $marketplace->getDescription();

                      // include the site name with the post:
                      $name = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title . ": $name";

                      $fb_data = array(
                        'message' => $this->view->translate('I just made ​​a purchase on the %s', $_SERVER['HTTP_HOST']),
                        'link' => $url,
                        'name' => $name,
                        'description' => $desc,
                      );

                      if( $picUrl ) $fb_data['picture'] = $picUrl;

                      $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                      return $this->_helper->redirector->gotoRoute(array(), 'marketplace_extended');
                }
              
              } catch( Exception $e ) {
                // Silence
              }
          endif;
        }
  
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
        //$this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

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

    public function createAction()
    {
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('marketplace', null, 'create')->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->navigation = $this->getNavigation();
        $this->view->form = $form = new Marketplace_Form_Create();

        $category_id = $this->_getParam('category', 0);    
        Engine_Api::_()->marketplace()->tree_list($category_id);
        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($category_id));
        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;
        $this->view->category_id = $category_id;	

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

		    if(Engine_Api::_()->marketplace()->authorizeIsActive()){
			    $form->authorize_login->setValue($mark['authorize_login']);
			    $form->authorize_key->setValue($mark['authorize_key']);
		    }else{
			    $form->business_email->setValue($mark['business_email']);
		    }

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
            
            if( !empty($_FILES) ) {
                $album = $marketplace->getSingletonAlbum();

                $params = array(
                  // We can set them now since only one album is allowed
                  'collection_id' => $album->getIdentity(),
                  'album_id' => $album->getIdentity(),
                  'marketplace_id' => $marketplace->getIdentity(),
                  'user_id' => $viewer->getIdentity(),
                );
                $noMainPhoto = $marketplace->photo_id ? false : true;
                foreach( $_FILES as $key => $file ) {
                    if( $key == 'photo' or !$file['tmp_name'] ) continue;
                    $photo_id = Engine_Api::_()->marketplace()->createPhoto($params, $file)->photo_id;

                    if( $noMainPhoto ){
                      $marketplace->photo_id = $photo_id;
                      $marketplace->save();
                      $noMainPhoto = false;
                    }
                } 
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
            //return $this->_helper->redirector->gotoRoute(array('marketplace_id' => $marketplace->marketplace_id), 'marketplace_success', true);
            return $this->_helper->redirector->gotoRoute(array('marketplace_id' => $marketplace->marketplace_id), 'marketplace_itempreview', true);
        } else {
            return $this->_helper->redirector->gotoUrl($marketplace->getHref(), array('prependBase' => false));
        }
    }
    
    
    public function itempreviewAction() {
        $marketplace_id = (int)$this->_getParam('marketplace_id', 0);
        $marketplace = Engine_Api::_()->getItem('marketplace', $marketplace_id);
        if( !$marketplace ) {
            return $this->_helper->redirector->gotoRoute(array(), 'marketplace_browse');
        }
        $this->view->marketplace = $marketplace;
        $this->view->album = $album = $marketplace->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($marketplace);
        
        $category_id = $marketplace->category_id;    
        Engine_Api::_()->marketplace()->tree_list($category_id);
        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($category_id));
        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;
    }

    public function editAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = $this->_helper->api()->user()->getViewer();
        $this->view->marketplace = $marketplace = Engine_Api::_()->getItem('marketplace', $this->_getParam('marketplace_id'));

        if (!$marketplace ) return $this->_forward('requireauth', 'error', 'core');
        
        if (!Engine_Api::_()->core()->hasSubject('marketplace')) {
            Engine_Api::_()->core()->setSubject($marketplace);
        }

        if( $this->_getParam('category') === null ) {
            return $this->_helper->redirector->gotoRoute(array('marketplace_id' => $marketplace->marketplace_id, 
                                                               'category' => $marketplace->category_id), 
                                                               'marketplace_edit', true);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;

        // Backup
        if ($viewer->getIdentity() != $marketplace->owner_id && !$this->_helper->requireAuth()->setAuthParams($marketplace, null, 'edit')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        
        $category_id = $this->_getParam('category', 0);   
        Engine_Api::_()->marketplace()->tree_list($category_id);
        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($category_id));
        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;
        $this->view->category_id = $category_id;

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
            $form->category_id->setValue((int)$this->_getParam('category', 0));

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
        
        $request = $this->getRequest()->getPost();
        if( !$form->isValid($request) ) {
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
            //echo "<pre>"; print_r($request); echo "</pre>"; die();
            foreach ($paginator as $photo) {
                $photoId = $photo->getIdentity();
                if( $marketplace->photo_id == $photo->file_id ) {
                    if( ( isset($request['delete_photo']) and $request['delete_photo'] ) or
                        ( isset($_FILES['photo']) and $_FILES['photo']['tmp_name'] ) ) {
                        $marketplace->photo_id = 0;
                        $marketplace->save();
                        $photo->delete();
                    } 
                    continue;
                }   
                if( ( isset($request['delete_photo_' . $photoId]) and $request['delete_photo_' . $photoId] ) or 
                    ( isset($_FILES['photo_' . $photoId]) and $_FILES['photo_' . $photoId]['tmp_name'] ) ) {
                    $photo->delete();
                }
                
                /*
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
                */

            }
            
            if( !empty($_FILES) ) {
              $album = $marketplace->getSingletonAlbum();
              $params = array(
                // We can set them now since only one album is allowed
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),
                'marketplace_id' => $marketplace->getIdentity(),
                'user_id' => $viewer->getIdentity(),
              );
              
              $noMainPhoto = $marketplace->photo_id ? false : true;
              if( isset($_FILES['photo']) and $_FILES['photo']['tmp_name'] ) {
                  $photo_id = Engine_Api::_()->marketplace()->createPhoto($params, $_FILES['photo'])->photo_id;
                  $marketplace->photo_id = $photo_id;
                  $marketplace->save();
                  $noMainPhoto = false;
                  unset( $_FILES['photo'] );
              }
              foreach( $_FILES as $key => $file ) {
                  if( !$file['tmp_name'] ) continue;
                  $photo_id = Engine_Api::_()->marketplace()->createPhoto($params, $file)->photo_id;
                  if( $noMainPhoto ){
                    $marketplace->photo_id = $photo_id;
                    $marketplace->save();
                    $noMainPhoto = false;
                  }
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
        return $this->_helper->redirector->gotoRoute(array('marketplace_id' => $marketplace->marketplace_id), 'marketplace_itempreview', true);
        //return $this->_redirect("marketplaces/manage");
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

        if (is_null($this->_navigation)) {

            $navigation = $this->_navigation = new Zend_Navigation();
            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('Browse Listings'),
                'route' => 'marketplace_browse',
                'module' => 'marketplace',
                'controller' => 'index',
                'action' => 'index'
            ));

            if ($this->_helper->api()->user()->getViewer()->getIdentity()) {

              $navigation->addPage(array(
                  'label' => Zend_Registry::get('Zend_Translate')->_('My Listings'),
                  'route' => 'marketplace_manage',
                  'module' => 'marketplace',
                  'controller' => 'index',
                  'action' => 'manage',
                  'active' => $active
              ));
            
              if(Engine_Api::_()->marketplace()->couponIsActive()) {
                $navigation->addPage(array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('My Coupons'),
                    'route' => 'marketplace_coupon',
                ));
		          }

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

			      }

		        if(Engine_Api::_()->marketplace()->cartIsActive()){
			        $navigation->addPage(array(
				        'label' => Zend_Registry::get('Zend_Translate')->_('My Cart'),
				        'route' => 'marketplace_general',
				        'action' => 'cart'
			        ));
		        }

            if ($this->_helper->api()->user()->getViewer()->getIdentity()) {
              $count = $ordersTable->getCountByUser($this->_helper->api()->user()->getViewer()->getIdentity());

              if ($count > 0) {
                  $navigation->addPage(array(
                      'label' => Zend_Registry::get('Zend_Translate')->_('Reports'),
                      'route' => 'marketplace_reports',
                      'module' => 'marketplace',
                      'controller' => 'index',
                      'action' => 'reports'
                  ));
              }
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
		
		$select = $select
					->where("owner_id = ? OR user_id = ?", $viewer->getIdentity())
					->order(($values['order'] == 'summ'?'CAST('.$values['order'].' AS DECIMAL)':$values['order']).' '.$values['order_direction']);

        // Make paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    }

    public function addtocartAction() {
		if( /*!$this->_helper->requireUser()->isValid() || */ !Engine_Api::_()->marketplace()->cartIsActive() )
		  return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Auth error')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
		
		$marketplace_id = $this->_getParam('marketplace_id', 0);
		
		if(empty($marketplace_id))
		  return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Marketplace Listing error')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
		
		$marketplace = Engine_Api::_()->getItem('marketplace', $marketplace_id);
		
		if(empty($marketplace))
		  return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Marketplace Listing error')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));

    //$inspection = (int)$this->_getParam('inspection', 0);
    $inspection = 1;

		$viewer = Engine_Api::_()->user()->getViewer();
    $cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');

		if( $viewer and $viewer->getIdentity()) {
		    $db = $cartTable->getAdapter();
		    $db->beginTransaction();

		    try
		    {
			    $alreadyCartItem = $cartTable->productIsAlreadyInCart($viewer->getIdentity(), $marketplace_id);
			    if(!$alreadyCartItem){
				    $cartTable->insert(array(
					    'user_id' => $viewer->getIdentity(),
					    'marketplace_id' => $marketplace_id,
					    'count' => 1,
              'inspection' => $inspection
				    ));
			    }else{
				    $alreadyCartItem->count++;
				    $alreadyCartItem->save();
			    }
			    $db->commit();
		    }
		    catch( Exception $e )
		    {
			    $db->rollBack();
			    throw $e;
		    }
    }
    else {
        $cartTable->addToCookieCart($marketplace_id, $inspection);
    }

		return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Listing has been added to cart')),
			'layout' => 'default-simple',
			'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
								  'fbpostaction' => 'cart'
								), 'marketplace_general', true),
		));
	}
	
    public function deletefromcartAction() {
		if( !Engine_Api::_()->marketplace()->cartIsActive() )
		  return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Auth error')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
		
		$marketplace_id = $this->_getParam('marketplace_id', 0);
		
		if(empty($marketplace_id))
		  return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Marketplace Listing error')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		  ));
		
		$marketplace = Engine_Api::_()->getItem('marketplace', $marketplace_id);
		$viewer = Engine_Api::_()->user()->getViewer();
		$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');

    if( $viewer and $viewer->getIdentity()) {
		    $db = $cartTable->getAdapter();
		    $db->beginTransaction();

		    try
		    {
			    $alreadyCartItem = $cartTable->productIsAlreadyInCart($viewer->getIdentity(), $marketplace_id);
			    if($alreadyCartItem){
				    $cartTable->delete(array(
					    'user_id = ?' => $viewer->getIdentity(),
					    'marketplace_id = ?' => $marketplace_id
				    ));
			    }else{
				    return $this->_forward('success', 'utility', 'core', array(
					    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Marketplace Listing error')),
					    'layout' => 'default-simple',
					    'parentRefresh' => true,
				    ));
			    }
			    $db->commit();
		    }
		    catch( Exception $e )
		    {
			    $db->rollBack();
			    throw $e;
		    }
    }
    else {
        $cartTable->deleteCookieCartItem($marketplace_id);
    }

		return $this->_forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Listing has been deleted from cart')),
			'layout' => 'default-simple',
			'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
								  'action' => 'cart'
								), 'marketplace_general', true),
		));
	}
	
    public function cartAction() {

		if( !Engine_Api::_()->marketplace()->cartIsActive() ) return $this->_forward('notfound', 'error', 'core');
		$viewer = Engine_Api::_()->user()->getViewer();
		$cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');

    if( $viewer and $viewer->getIdentity()) {
		    $db = $cartTable->getAdapter();
		
		    if( $this->getRequest()->isPost() )
		    {
			    $values = $this->getRequest()->getPost();

			    if(!empty($values['redirect']) && $values['redirect'] == '1'){
				    return $this->_helper->redirector->gotoRoute(array('action' => 'checkout'));
			    }elseif(!empty($values['redirect']) && $values['redirect'] == '2'){
				    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
			    }

			    if($values['marketplaces_count']){
				    foreach($values['marketplaces_count'] as $marketplace_id => $m_count){
					    $m_count = intval($m_count);
              $inspection = 1; //isset($values['marketplaces_inspection'][$marketplace_id]) ? 1 : 0;
					    if($m_count > 0){
						    $cartTable->update(array(
							    'count' => $m_count,
                  'inspection' => $inspection
						    ),array(
							    'user_id = ?' => $viewer->getIdentity(),
							    'marketplace_id = ?' => $marketplace_id
						    ));
					    }elseif($m_count == 0){
						    $cartTable->delete(array(
							    'user_id = ?' => $viewer->getIdentity(),
							    'marketplace_id = ?' => $marketplace_id
						    ));
					    }
				    }
			    }
		    }

		    $select = $cartTable->select()
			    ->where('user_id = ?', $viewer->getIdentity())
		    ;
        $cartitems = $select->query()->fetchAll();
    }
    else {
        $cartitems = $cartTable->getCookieCart(false);
        if( $this->getRequest()->isPost() ){
			      $values = $this->getRequest()->getPost();
            if(!empty($values['redirect']) && $values['redirect'] == '1'){
				      return $this->_helper->redirector->gotoRoute(array('action' => 'checkout'));
			      }elseif(!empty($values['redirect']) && $values['redirect'] == '2'){
				      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
			      } 

            if($values['marketplaces_count']){
			        $cartTable->updateCookieCartItemArray($values['marketplaces_count'], $values['marketplaces_inspection']);
              foreach($values['marketplaces_count'] as $marketplace_id => $m_count) { 
                $cartitems[$marketplace_id]['count'] = $m_count;
                $cartitems[$marketplace_id]['inspection'] = true; //isset($values['marketplaces_inspection'][$marketplace_id]) ? true : false;
              }
			      }
        }
    }
    $this->view->cartitems = $cartitems;		
		$this->view->flat_shipping_rate = $flat_shipping_rate = floatval(Engine_Api::_()->getApi('settings', 'core')->getSetting('flat.shipping.rate', 0)); 
	}
	
    public function shippinginfoAction() 
    {
      if( !Engine_Api::_()->marketplace()->cartIsActive() ) return $this->_forward('notfound', 'error', 'core');
	    $viewer = Engine_Api::_()->user()->getViewer();

      if( !$viewer->getIdentity() ) return $this->_helper->redirector->gotoRoute(array(), 'user_login');
	
	    $cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
      $shippinginfoTable = Engine_Api::_()->getDbtable('shippinginfo', 'marketplace');

      $this->view->form = $form = new Marketplace_Form_Shippinginfo();

      $select = $cartTable->select()->where('user_id = ?', $viewer->getIdentity());
      $cartitems = $select->query()->fetchAll();

      $currentInfo = $shippinginfoTable->getInfo();
      if( !empty($currentInfo) ) {
        $form->populate($currentInfo);
      } else {
        $form->name->setValue($viewer->displayname);
        $form->email->setValue($viewer->email);
      }

      $this->view->cartitems = $cartitems;

      if( $this->getRequest()->isPost() ) {

			  if( $form->isValid($this->getRequest()->getPost()) ) {
            $values = $form->getValues();
        } else {
            return;
        }
        $siId = $shippinginfoTable->saveInfo($values);
        /*if( !$viewer->getIdentity()) {
          $cartTable->updateCookieShippingInfo($values);
        }*/
        return $this->_helper->redirector->gotoRoute(array('action' => 'checkout', 'sinfo' => $siId), 'marketplace_general');
      }
    }

    public function checkoutAction() {
		
		  if( !Engine_Api::_()->marketplace()->cartIsActive() ) return $this->_forward('notfound', 'error', 'core');
		  $viewer = Engine_Api::_()->user()->getViewer();

      if( !$viewer->getIdentity() ) return $this->_helper->redirector->gotoRoute(array(), 'user_login');

      $shippinginfoTable = Engine_Api::_()->getDbtable('shippinginfo', 'marketplace');
      $this->view->shippingInfo = $shippinginfoTable->getInfo();
		
		  $cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');

      if( $viewer and $viewer->getIdentity()) {

        $viewer_id = $viewer->getIdentity();
	      $db = $cartTable->getAdapter();
	      $select = $cartTable->select()
		      ->where('user_id = ?', $viewer_id)
	      ;
	      $cartitems = $select->query()->fetchAll();
      } else {
        $cartitems = $cartTable->getCookieCart();
        $viewer_id = 0;
      }
      $this->view->cartitems = $cartitems;

	    $this->view->flat_shipping_rate = $flat_shipping_rate = floatval(Engine_Api::_()->getApi('settings', 'core')->getSetting('flat.shipping.rate', 0));
      $this->view->inspection_fee = $inspection_fee = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspection', 0);
	/*
	    if(Engine_Api::_()->marketplace()->couponIsActive()){
		    $couponTable = Engine_Api::_()->getDbTable('coupons', 'marketplace');
		    $couponTableName = $couponTable->info('name');
		    $couponcartTable = Engine_Api::_()->getDbTable('couponcarts', 'marketplace');
		    $couponcartTableName = $couponcartTable->info('name');
		
		    if($this->getRequest()->getPost() && $cartitems->toArray()){
			    $coupon_code = $this->_getParam('coupon_code', '');
			    $first_marketplace = Engine_Api::_()->getItem('marketplace', $cartitems[0]['marketplace_id']);
			    if($coupon_code){
				    $coupon_select = $couponTable->select()->where('code = ?', $coupon_code);
				    $coupon_res = $couponTable->fetchRow($coupon_select);
				
				    $seller_coupon = Engine_Api::_()->marketplace()->getDiscountPercentByOwner($coupon_res->user_id, $viewer->getIdentity());
				    if($coupon_res->coupon_id && empty($seller_coupon)){
					    if($coupon_res->user_id != $first_marketplace->getOwner()->getIdentity()){
						    $this->view->coupon_error = 1;//wrong coupon code
					    }else{
						    $couponcartTable->insert(array(
							    'coupon_id' => $coupon_res->coupon_id,
							    'user_id' => $viewer->getIdentity()
						    ));
						    $this->view->coupon_error = 2;
					    }
				    }elseif(!empty($seller_coupon)){
					    $this->view->coupon_error = 3;
				    }else{
					    $this->view->coupon_error = 1;
				    }
			    }
		    }
	    }
*/
	    if( is_array($cartitems) ) {
		    $first_marketplace = Engine_Api::_()->getItem('marketplace', $cartitems[0]['marketplace_id']);
		    if(!$first_marketplace)
			    return;
		    $first_stage_owner = $first_marketplace->getOwner()->getIdentity();
		
		    if(Engine_Api::_()->marketplace()->authorizeIsActive()){
			    $flat_shipping_rate = floatval(Engine_Api::_()->getApi('settings', 'core')->getSetting('flat.shipping.rate', 0));
			    $first_marketplace = Engine_Api::_()->getItem('marketplace', $cartitems[0]['marketplace_id']);
			    $ids_arr = array();
			    $titles_arr = array();
			    $shipping_fee = 0;
			    $total_amount = 0;
			    $discount_amount = 0;
			    foreach($cartitems as $cartitem){
				    $current_marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']);
				    //if($first_stage_owner != $current_marketplace->getOwner()->getIdentity())
					  //  continue;

				    //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($cartitem->marketplace_id, $viewer_id);
            $product_shipping_fee = $marketplace->shipping;

				    $shipping_fee += ($product_shipping_fee?$product_shipping_fee:$flat_shipping_rate) * $cartitem->count;
				    $total_amount += $current_marketplace->price * $cartitem['count'];
						
				    $coupon_discount = Engine_Api::_()->marketplace()->getDiscount($cartitem['marketplace_id'], $viewer_id);
				    $discount_amount += $coupon_discount * $cartitem['count'];
				
				    $titles_arr[] = $current_marketplace->title;
				    $ids_arr[] = $current_marketplace->marketplace_id.'-'.$cartitem['count'];
			    }
			    $total_amount_full = $total_amount + $shipping_fee - $discount_amount;
			
			    if ($first_marketplace) {

				    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $first_marketplace->owner_id);
			    }
			
			    $authorize_login = ($first_marketplace->authorize_login?$first_marketplace->authorize_login:'7sBYqTp344eh');
			    $authorize_key = ($first_marketplace->authorize_key?$first_marketplace->authorize_key:'8sY8eUVj47M46dxA');
			    $testmode = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.authorize.testmode', '0');
			    $myAuthorize = new Marketplaceauthorize_Api_Authorize();
			    $myAuthorize->setUserInfo($authorize_login, $authorize_key);
			    $myAuthorize->addField('x_Receipt_Link_URL', 'http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl().'/marketplaces/paymentreturn/payment/authorize');
			    $myAuthorize->addField('x_Relay_URL', 'http://'.$_SERVER['HTTP_HOST'].$this->view->baseUrl().'/marketplaces/paymentnotify/payment/authorize');
			    $myAuthorize->addField('x_Description', join(', ', $titles_arr));
			    $myAuthorize->addField('x_Amount', number_format($total_amount_full, 2));
			    $myAuthorize->addField('x_Invoice_num', join('|', $ids_arr) . ':' . $viewer_id);
			    $myAuthorize->addField('x_product_id', join('|', $ids_arr) . ':' . $viewer_id);
			    $myAuthorize->addField('x_user_id', $viewer_id);
			    $myAuthorize->enableTestMode();
			    $this->view->paymentForm = $myAuthorize->render();
		    }else{
			    $this->view->paymentForm = $this->paypalcart($cartitems)->form();
		    } //authorizeIsActive()
	    } // $cartitems->toArray()

    }

    public function paypalcart($cartitems) {

      $paypal = new Marketplace_Api_Payment($this->_sandbox);//true);
      $viewer = $this->_helper->api()->user()->getViewer();

      if( $viewer and $viewer->getIdentity()) {
        $viewer_id = $viewer->getIdentity();
      } else {
        $viewer_id = 0;
      }

		  $flat_shipping_rate = floatval(Engine_Api::_()->getApi('settings', 'core')->getSetting('flat.shipping.rate', 0));
      
		  //$first_marketplace = Engine_Api::_()->getItem('marketplace', $cartitems[0]['marketplace_id']);
		  //$first_stage_owner = $first_marketplace->getOwner()->getIdentity();

		  $mids = array();		
		  $ids_arr = array();
		  $titles_arr = array();
		  $shipping_fee = 0;
		  $total_amount = 0;
		  $total_inspection = 0;
		  $discount_amount = 0;
      $inspectionEnable = Engine_Api::_()->marketplace()->isInspectionEnable();

		  foreach($cartitems as $cartitem){
			  $current_marketplace = Engine_Api::_()->getItem('marketplace', $cartitem['marketplace_id']);

			  //if($first_stage_owner != $current_marketplace->getOwner()->getIdentity())
				//  continue;
			  //$product_shipping_fee = Engine_Api::_()->marketplace()->getShipingFee($cartitem['marketplace_id'], $viewer_id);
        $product_shipping_fee = $current_marketplace->shipping;

			  $shipping_fee += ($product_shipping_fee?$product_shipping_fee:$flat_shipping_rate) * $cartitem['count'];
			  $total_amount += $current_marketplace->price * $cartitem['count'];
        
        if( $inspectionEnable ) {
          $inspection_fee = Engine_Api::_()->marketplace()->getInspectionFee($current_marketplace->price);
          $total_inspection += $cartitem['inspection'] ? $inspection_fee * $cartitem['count'] : 0;
        }
	
			  $coupon_discount = Engine_Api::_()->marketplace()->getDiscount($cartitem['marketplace_id'], $viewer_id);
			  $discount_amount += $coupon_discount * $cartitem['count'];
			
			  $titles_arr[] = $current_marketplace->title;

        //$inspectionMode = ($cartitem['inspection'] and $inspectionEnable) ? '+' : '';
			  //$ids_arr[] = $current_marketplace->marketplace_id.'-'.$cartitem['count'].$inspectionMode;
        $ids_arr[] = $current_marketplace->marketplace_id.'-'.$cartitem['count'];

        $mids[] = $current_marketplace->marketplace_id;
		  }
		  $total_amount_full = $total_amount + $shipping_fee + $total_inspection - $discount_amount;
		
      //if ($first_marketplace) {
      //  $this->view->owner = $owner = Engine_Api::_()->getItem('user', $first_marketplace->owner_id);
      //}
      $midsStr = implode('_', $mids);
      
      //$paypal->setBusinessEmail($first_marketplace->business_email);
      $paypal->setBusinessEmail( Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.mainpaypal', $first_marketplace->business_email) );

      if( $viewer_id ) $paypal->setPayer($viewer->email, $viewer_id);
      else $paypal->setPayer('', $viewer_id);

      $paypal->setAmount(number_format($total_amount_full, 2));
      $paypal->setNumber(join('|', $ids_arr) . ':' . $viewer_id);
      $paypal->addItem(array('item_name' => join(', ', $titles_arr)));
      $paypal->setControllerUrl("http://" . $this->getRequest()->getHttpHost() . $this->view->url(array(), 'marketplace_extended', true) . '/payment'); //->url());
      $paypal->setReturn( $paypal->paypalData['returnUrl']."/mids/".$midsStr );

      return $paypal;
   }

    public function ajaxlikeAction() {
        $marketplace_id = (int)$this->_getParam('marketplace_id', 0);
        $marketplace = Engine_Api::_()->getItem('marketplace', $marketplace_id);
        if( $marketplace ) {
            $marketplace->updateLikes();
        }
        die();
    }

    public function commentsAction() {
        $marketplace_id = (int)$this->_getParam('marketplace_id', 0);
        $marketplace = Engine_Api::_()->getItem('marketplace', $marketplace_id);
        if( !$marketplace ) {
            return $this->_helper->redirector->gotoRoute(array(), 'marketplace_browse');
        }
  
        Engine_Api::_()->marketplace()->tree_list($marketplace->category_id);
        $a_tree = Engine_Api::_()->marketplace()->tree_list_load_array(array($marketplace->category_id));
        $this->view->a_tree = $a_tree;
        $this->view->urls = $this->_helper->url;	

        $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
        $this->view->likeList = $likesTable->getLikePaginator($marketplace, 5);

        $this->view->marketplace = $marketplace;
    }
}

