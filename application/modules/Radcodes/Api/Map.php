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
 
class Radcodes_Api_Map extends Core_Api_Abstract
{

	/*
	 * @todo: need to refactor .. use factory instead, and do not create dependency here
	 */
	public function getInstance($name, $options=array())
	{
		Zend_Registry::get('Zend_View')->addHelperPath(APPLICATION_PATH . '/application/modules/Gmap/View/Helper', 'Gmap_View_Helper');
		
    return Radcodes_Lib_Google_Map::getInstance($name);
	}
  
	public function factory($name, $options = array())
	{
		return Radcodes_Lib_Google_Map::getInstance($name);
	}	
	
}