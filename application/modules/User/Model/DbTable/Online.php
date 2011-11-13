<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Online.php 9239 2011-09-06 18:51:17Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Model_DbTable_Online extends Engine_Db_Table
{
  public function check(User_Model_User $user)
  {
    // No CLI
    if( 'cli' === PHP_SAPI ) {
      return;
    }
    
    // Prepare
    $id = (int) $user->getIdentity();
    
    // Get ip address
    $db = $this->getAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
    
    // Run update first
    $count = $this->update(array(
      'active' => date('Y-m-d H:i:s'),
    ), array(
      'user_id = ?' => $id,
      'ip = ?' => $ipExpr,
      'active > ?' => new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'),
    ));

    // Run insert if update doesn't do anything
    if( $count < 1 ) {
      if( $this->getAdapter() instanceof Zend_Db_Adapter_Mysqli ||
          $this->getAdapter() instanceof Engine_Db_Adapter_Mysql ||
          $this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql ) {
        $sql = 'INSERT IGNORE INTO `'.$this->info('name').'` (`user_id`, `ip`, `active`) VALUES (?, UNHEX(?), ?)';
        $sql = $this->getAdapter()->quoteInto($sql, $id, null, 1);
        $sql = $this->getAdapter()->quoteInto($sql, bin2hex($ipObj->toBinary()), null, 1);
        $sql = $this->getAdapter()->quoteInto($sql, date('Y-m-d H:i:s'), null, 1);
        $this->getAdapter()->query($sql);
      } else {
        $this->insert(array(
          'user_id' => $id,
          'ip' => $ipExpr,
          'active' => date('Y-m-d H:i:s'),
        ));
      }
    }

    return $this;
  }

  public function gc()
  {
    $this->delete(array('active < ?' => new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)')));
    return $this;
  }
}