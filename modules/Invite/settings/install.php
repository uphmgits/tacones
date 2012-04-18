<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: install.php 9405 2011-10-18 23:07:04Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Invite_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    if( method_exists($this, '_addGenericPage') ) {
      $this->_addGenericPage('invite_index_index', 'Invite', 'Invite Page', '');
    } else {
      $this->_error('Missing _addGenericPage method');
    }
    
    parent::onInstall();
  }
}
 