<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: AdminCouponsController.php 7244 2010-09-01 01:49:53Z jaa $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_CouponController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init() {
	if( !Engine_Api::_()->marketplace()->couponIsActive() ) return $this->_forward('notfound', 'error', 'core');
  }
  
  public function indexAction()
  {
	$viewer = Engine_Api::_()->user()->getViewer();
	if(!$viewer->getIdentity()){
      return $this->_forward('requireauth', 'error', 'core');
	}
    $this->view->navigation = $navigation = $this->getNavigation();

    $page=$this->_getParam('page',1);
	$couponTable = Engine_Api::_()->getDbTable('coupons', 'marketplace');
	$select = $couponTable->select()->where('user_id = ?', $viewer->getIdentity());
	
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function createAction()
  {
    $this->view->form = $form = new Marketplace_Form_Coupon_Edit();
	$viewer = Engine_Api::_()->user()->getViewer();
    
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

	$couponTable = Engine_Api::_()->getDbTable('coupons', 'marketplace');
	$coupon_select = $couponTable->select()
		->where('code = ?', $values['code'])
	;
	$coupon_res = $couponTable->fetchRow($coupon_select);
	if(!empty($coupon_res))
		return $form->getElement('code')->addError('Please select another code. This coupon code is already used.');

	$coupon = $couponTable->createRow();
	$values['percent'] = intval($values['percent']);
	if($values['percent'] > 100){
		$values['percent'] = 100;
	}elseif($values['percent'] <= 0){
		$values['percent'] = 1;
	}
	$values['user_id'] = $viewer->getIdentity();
	$coupon->setFromArray($values);
	$coupon->save();
    
	// Forward
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Your changes have been saved.')
    ));
  }

  public function editAction()
  {
    $id = $this->_getParam('id', null);
    $coupon = Engine_Api::_()->getItem('marketplace_coupon', $id);
	$viewer = Engine_Api::_()->user()->getViewer();
	if(!$coupon->getIdentity() || $coupon->user_id != $viewer->getIdentity()){
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Identity error')
      ));
	}
    $this->view->form = $form = new Marketplace_Form_Coupon_Edit();
    $form->populate($coupon->toArray());
    
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();
	
	$couponTable = Engine_Api::_()->getDbTable('coupons', 'marketplace');
	$coupon_select = $couponTable->select()
		->where('code = ?', $values['code'])
	;
	$coupon_res = $couponTable->fetchRow($coupon_select);
	if(!empty($coupon_res))
		return $form->getElement('code')->addError('Please select another code. This coupon code is already used.');
	
	$values['percent'] = intval($values['percent']);
	if($values['percent'] > 100){
		$values['percent'] = 100;
	}elseif($values['percent'] <= 0){
		$values['percent'] = 1;
	}
	$values['user_id'] = $viewer->getIdentity();
    
	$coupon->setFromArray($values);
    $coupon->save();
    // Forward
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Your changes have been saved.')
    ));
  }

  public function deleteAction()
  {
	$couponcartTable = Engine_Api::_()->getDbTable('couponcarts', 'marketplace');
	$couponcartTableName = $couponcartTable->info('name');

    $id = $this->_getParam('id');
    $this->view->coupon_id=$id;
	
	$viewer = Engine_Api::_()->user()->getViewer();
	$coupon = Engine_Api::_()->getItem('marketplace_coupon', $id);
	if(!$coupon->getIdentity() || $coupon->user_id != $viewer->getIdentity()){
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Identity error')
      ));
	}
	
    // Check post
    if( $id )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $coupon->delete();
		
		$couponcartTable->delete(
			array('coupon_id' => $id)
		);
		
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh'=> true,
          'messages' => array('Successfully deleted')
      ));
    }

  }

    // Utility
    public function getNavigation($active = false) {
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

                $navigation->addPage(array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('My Coupons'),
                    'route' => 'marketplace_coupon',
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
					
				if(Engine_Api::_()->marketplace()->cartIsActive()){
					$navigation->addPage(array(
						'label' => Zend_Registry::get('Zend_Translate')->_('My Cart'),
						'route' => 'marketplace_general',
						'action' => 'cart'
					));
				}
				
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

}