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
 


class Radcodes_Lib_Validate_Location_Address extends Zend_Validate_Abstract
{
  const INVALID    = 'invalid';
  
  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::INVALID    => "'%value%' does not appear to be a valid location.",
  );


  /**
   * Defined by Zend_Validate_Interface
   *
   * Returns true if and only if $value is a valid license
   *
   * @param  mixed $value
   * @return boolean
   */
  public function isValid($value)
  {
  	$this->_setValue($value);
  	
    $google_map = new Radcodes_Lib_Google_Map();
    $geocoded_address = $google_map->geocode($value);
    if (!$geocoded_address)
    {
    	$this->_error(self::INVALID);
    	return false;
    }

    return true;
  }
  

}

