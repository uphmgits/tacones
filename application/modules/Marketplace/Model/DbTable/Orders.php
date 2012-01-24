<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Albums.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_DbTable_Orders extends Engine_Db_Table
{
  protected $_rowClass = 'Marketplace_Model_Order';
  protected $_name = 'marketplace_orders';

  public function getCountByUser($user_id) {
	$table  = Engine_Api::_()->getDbTable('orders', 'marketplace');
    $rName = $table->info('name');
    $select = $table->select()
		->from($rName)
		->where('owner_id = ?', $user_id);
	$stmt = $this->getAdapter()->query($select);
	$result = $stmt->rowCount();
	return $result;
  }
}