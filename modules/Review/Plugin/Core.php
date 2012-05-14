<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('reviews', 'review');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'review');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete reviews
      $reviewTable = Engine_Api::_()->getDbtable('reviews', 'review');
      $reviewSelect = $reviewTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach( $reviewTable->fetchAll($reviewSelect) as $review ) {
        $review->delete();
      }
      
      $reviewSelect = $reviewTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $reviewTable->fetchAll($reviewSelect) as $review ) {
        $review->delete();
      }
      
      // delete images and albums as well
    }
  }
}