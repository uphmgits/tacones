<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 9339 2011-09-29 23:03:01Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Messages_Api_Core extends Core_Api_Abstract
{
  public function getUnreadMessageCount(Core_Model_Item_Abstract $user)
  {
    $identity = $user->getIdentity();

    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $select = Engine_Api::_()->getDbtable('recipients', 'messages')->select()
      ->setIntegrityCheck(false)
      ->from($rName, new Zend_Db_Expr('COUNT(conversation_id) AS unread'))
      ->where($rName.'.user_id = ?', $identity)
      ->where($rName.'.inbox_deleted = ?', 0)
      ->where($rName.'.inbox_read = ?', 0);

    $data = Engine_Api::_()->getDbtable('recipients', 'messages')->fetchRow($select);
    return (int) $data->unread;
  }
}
