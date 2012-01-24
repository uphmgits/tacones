<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package  Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license  http://www.radcodes.com/license/
 * @version  $Id$
 * @author   Vincent Van <vincent@radcodes.com>
 */
 


class Radcodes_Lib_Rest_Store extends Radcodes_Lib_Rest
{
	
	public function getModules()
	{
	  
		$modules = Engine_Api::_()->getDbtable('modules', 'core')->getModules();
		
		$compactModules = array();
		foreach ($modules as $module) {
			$compactModules[$module->name] = array_merge(
			  $module->toArray(),
			  array('license' => Engine_Api::_()->getApi('settings', 'core')->getSetting($module->name.'.license'))
			);
		}
		
    $radcodesModules = $this->callMethod('getModules', array('modules'=>$compactModules));
    
    if ($radcodesModules === false)
    {
    	$radcodesModules = array();
    }
    
		return $radcodesModules;
	}
	
	
	public function getCustomer()
	{
		$customer = $this->callMethod('getCustomer');
		return $customer;
	}
	
	
	public function getProduct($name)
	{
		$product = $this->callMethod('getProduct', array('name'=>$name));
		return $product;
	}
	
	
	public function getLicense($product)
	{
		$license = $this->callMethod('getLicense', array('product'=>$name));
		return $license;
	}
	
	
	public function enableModules($names = array())
	{
		if (!is_array($names)) {
			$names = explode(',', $names);
		}
		if (!empty($names)) {
			$data = array('enabled' => 1);
			$where = "name IN ('".join("','",$names)."')";
			Engine_Api::_()->getDbtable('modules', 'core')->update($data, $where);
		}
	}
	
	
	public function disableModules($names = array())
	{
	  if (!is_array($names)) {
      $names = explode(',', $names);
    }
    if (!empty($names)) {
      $data = array('enabled' => 0);
      $where = "name IN ('".join("','",$names)."')";
      Engine_Api::_()->getDbtable('modules', 'core')->update($data, $where);
    }
	}
	
	
	public function verifyLicense($license, $domain, $type)
	{
		$params = array('license'=>$license,'domain'=>$domain,'type'=>$type);
    $result = $this->callMethod('verifyLicense', $params);
    return $result;
	}
}

