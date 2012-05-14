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
 
class Radcodes_Api_Filter extends Core_Api_Abstract
{

	public function removeKeyEmptyValues($values, $options=array())
	{
    foreach ($values as $key => $value)
    {
    	if (is_array($value))
    	{
    		$values[$key] = $value = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($value, $options);
    	}

      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
      
    }
    return $values;
	}
  
}