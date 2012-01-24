<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('marketplaces', 'marketplace');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'marketplace');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete marketplaces
      $marketplaceTable = Engine_Api::_()->getDbtable('marketplaces', 'marketplace');
      $marketplaceSelect = $marketplaceTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $marketplaceTable->fetchAll($marketplaceSelect) as $marketplace ) {
        $marketplace->delete();
      }
      // delete images and albums as well
    }
  }
}