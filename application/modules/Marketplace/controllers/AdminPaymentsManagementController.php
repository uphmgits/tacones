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
    $ordersTable = Engine_Api::_()->getDbTable('orders', 'marketplace');

    $select = $ordersTable->select()->where("status = 'sold'")->order('order_id DESC');

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

  public function refundsAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_paymentsmanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_paymentsmanagement', array(), 'marketplace_admin_main_paymentsmanagement_refunds');

  }
}
