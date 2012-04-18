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
 


class Radcodes_Lib_Validate_License extends Zend_Validate_Abstract
{
  const INVALID    = 'invalid';
  const INVALID_FORMAT = 'invalidFormat';
  /**
   * @var array
   */
  protected $_messageTemplates = array(
    self::INVALID    => "'%value%' does not appear to be a valid license.",
    self::INVALID_FORMAT => "'%value%' is not in correct format of XXXX-XXXX-XXXX-XXXX.",
  );

  /**
   * License Type
   * 
   * @var string
   */
  protected $_type;
  

  public function __construct($type)
  {
  	$this->setType($type);
  }
  
  /**
   * Return license type option
   * 
   * @return string
   */
  public function getType()
  {
  	return $this->_type;
  }
  

  
  /**
   * Sets the type option
   *
   * @param  string $type
   * @return Radcodes_Lib_Validate_License Provides a fluent interface
   */
  public function setType($type)
  {
    $this->_type = $type;
    return $this;
  }  
  

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
  	
  	if (!$this->isValidFormat($value))
  	{
  		$this->_error(self::INVALID_FORMAT);
  		return false;
  	}
  	try
  	{
	  	$domain = $_SERVER['SERVER_NAME'];
	    $result = Engine_Api::_()->radcodes()->getRest('store')->verifyLicense($value, $domain, $this->_type);
	    
	    if (is_array($result) && !empty($result['error']))
	    {
	      if (isset($result['message']))
	      {
	        $this->setMessage($result['message'], self::INVALID);
	      }
	      $this->_error(self::INVALID);
	      return false;
	    }
  	}
    catch (Zend_Exception $ex)
    {
    	return true; // let pass
    }

    return true;
  }
  
  
  protected function isValidFormat($value)
  {
    $license = trim($value);
    if( !preg_match("/^[a-zA-Z0-9]{4}[-]{1}[a-zA-Z0-9]{4}[-]{1}[a-zA-Z0-9]{4}[-]{1}[a-zA-Z0-9]{4}$/", $license) ) {
      return false;
    }
    return true;
  }
}

