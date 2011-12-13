<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Marketplaces.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Model_DbTable_Marketplaces extends Engine_Db_Table
{
  protected $_rowClass = "Marketplace_Model_Marketplace";

  public function getlastemail($user_id)
  {

    $table  = Engine_Api::_()->getDbTable('marketplaces', 'marketplace');
    $rName = $table->info('name');
    $select = $table->select()
            ->from($rName)
      ->where('owner_id = ?', $user_id)
            ->order('modified_date DESC')
      ->limit(1);

    $email = $table->fetchRow($select);
    return $email;
  }
}