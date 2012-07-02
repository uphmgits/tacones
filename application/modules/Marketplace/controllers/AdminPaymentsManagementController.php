<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_AdminPaymentsManagementController extends Core_Controller_Action_Admin
{
  public function indexAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_complete');

    $page = $this->_getParam('page', 1);
    $this->view->status_filter = $status_filter = $this->_getParam('status_filter', 'all');

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $select = $ordersTable->select()->where("status = 'sold'");

    $formFilter = new Marketplace_Form_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      switch( $status_filter ) {
        case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
        case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
        case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
        case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
        case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
      }
    }
    $select->order("order_id DESC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

  public function refundsAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_refunds');

    $page = $this->_getParam('page', 1);
    $this->view->status_filter = $status_filter = $this->_getParam('status_filter', 'all');

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $select = $ordersTable->select()->where("status = 'canceled'");

    $formFilter = new Marketplace_Form_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      switch( $status_filter ) {
        case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
        case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
        case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
        case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
        case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
      }
    }
    $select->order("order_id DESC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

  }

  public function failedAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_failed');

    $page = $this->_getParam('page', 1);
    $this->view->status_filter = $status_filter = $this->_getParam('status_filter', 'all');

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $select = $ordersTable->select()->where("status = 'failed'");

    $formFilter = new Marketplace_Form_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      switch( $status_filter ) {
        case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
        case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
        case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
        case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
        case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
      }
    }
    $select->order("order_id DESC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

  }

  public function returnsAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_returns');

    $page = $this->_getParam('page', 1);
    $this->view->status_filter = $status_filter = $this->_getParam('status_filter', 'all');

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $select = $ordersTable->select()->where("status = 'return'");

    $formFilter = new Marketplace_Form_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      switch( $status_filter ) {
        case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
        case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
        case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
        case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
        case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
      }
    }
    $select->order("order_id DESC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

  }

  public function archiveAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_archive');

    $page = $this->_getParam('page', 1);
    $this->view->status_filter = $status_filter = $this->_getParam('status_filter', 'all');

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $select = $ordersTable->select()->where("status LIKE '%done%'");

    $formFilter = new Marketplace_Form_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
      switch( $status_filter ) {
        case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
        case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
        case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
        case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
        case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
      }
    }
    $select->order("order_id DESC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

}
