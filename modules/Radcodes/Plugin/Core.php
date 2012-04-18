<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_Plugin_Core
{
  public function getAdminNotifications($event)
  {
  	$lastupdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('radcodes.storelastupdatetime', 0);
  	$currentTime = time();
  	
  	if ($currentTime > $lastupdate + 86400)
  	{
  		// to be updated .. check for new modules
  		$modules = Engine_Api::_()->radcodes()->getRest('store')->getModules();
  		
  		Engine_Api::_()->getApi('settings', 'core')->setSetting('radcodes.storelastupdatetime', $currentTime);
  	}
  	
  	return ;
  }
}