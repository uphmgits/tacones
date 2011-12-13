<?php
/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_DbTable_Cart extends Engine_Db_Table
{
	protected $_rowClass = 'Marketplace_Model_Cart';
	protected $_name = 'marketplace_cart';

	public function productIsAlreadyInCart($user_id = 0, $marketplace_id = 0) {
		if($user_id == 0 || $marketplace_id == 0)
			return false;
		
		$select = $this->select()
			->where('marketplace_id = ?', $marketplace_id)
			->where('user_id = ?', $user_id)
		;
		return $this->fetchRow($select);
	}
}