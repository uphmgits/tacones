<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_Coupon extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'marketplace';
  protected $_owner_type = 'marketplace';
  protected $_collection_type = 'marketplace_coupons';

  protected function _delete()
  {
	if(Engine_Api::_()->marketplace()->couponIsActive()){
		// Delete all child posts
		$couponcartTable = Engine_Api::_()->getDbTable('couponcarts', 'marketplace');
		$couponcartTable->delete(array('coupon_id = ?' => $this->getIdentity()));
	}

    parent::_delete();
  }
}