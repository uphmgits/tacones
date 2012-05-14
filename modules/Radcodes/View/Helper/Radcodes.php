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

class Radcodes_View_Helper_Radcodes extends Zend_View_Helper_Abstract
{

  public function __call($name, $arguments)
  {
  	$class = 'Radcodes_View_Helper_Radcodes_'.ucfirst($name);
  	try
  	{
  		$helper = new $class();
  		$helper->setView($this->view);
  		
  		return $helper;
  	}
  	catch (Excpetion $ex)
  	{
  		throw new Engine_Exception("Could not load class::$class in Radcodes_View_Helper_Radcodes");
  	}
  }	
	
  public function radcodes()
  {
    return $this;
  }
  /*
  public function string()
  {
    return $this->text();
  }
  
  public function text()
  {
    return new Radcodes_Lib_Helper_Text();
  }
  
  public function date()
  {
    return new Radcodes_Lib_Helper_Date();
  }
  
  public function number()
  {
    return new Radcodes_Lib_Helper_Number();
  }
  
  public function unit()
  {
    return new Radcodes_Lib_Helper_Unit();
  }
  
  public function map()
  {
    return new Radcodes_View_Helper_Radcodes_Map();
  }
  */
}