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
 
 
 
class Radcodes_Model_Location extends Core_Model_Item_Abstract
{
  // Properties
  protected $_searchColumns = false;

  protected $_parent = null;
  
  /**
   * Gets an absolute URL to the listing to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    return $this->getParent()->getHref();
  }

  public function getOwner($type = null)
  {
    $parent = $this->getParent();
    if( null === $type && $type !== $parent->getType() ) {
      return $parent->getOwner($type);
    }
    return $parent;
  }

  public function getParent($recurseType = null)
  {
    if ($this->_parent === null)
    {
      $this->_parent = parent::getParent();
    }
    return $this->_parent;
  }
  
  
  public function setParent($parent)
  {
    $this->_parent = $parent;
  }
  
  public function __toString()
  {
    return $this->getParent()->__toString();
  }  
  

  public function getCountryName($locale = null)
  {
  	if ($this->country)
  	{
  		if (!$locale)
  		{
  			$locale = Zend_Registry::get('Zend_Translate')->getLocale();
  		}
      
      $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
      return isset($territories[$this->country]) ? $territories[$this->country] : $this->country;
  	}
  }
  
}