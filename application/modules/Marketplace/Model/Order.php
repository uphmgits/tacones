<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Photo.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_Order extends Core_Model_Item_Abstract//Core_Model_Item_Collectible
{
  protected $_parent_type = 'marketplace';
  protected $_owner_type = 'marketplace';
  protected $_collection_type = 'marketplace_orders';
}