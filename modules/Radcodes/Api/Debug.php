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
 
class Radcodes_Api_Debug extends Core_Api_Abstract
{
  
  protected $_log;
	
  protected $_logs;
  
  protected function _initLog()
  {
  	if (!($this->_log instanceof Zend_Log))
  	{
      $this->_log = new Zend_Log();
      $this->_log->addWriter(new Zend_Log_Writer_Firebug());
      $this->_log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/radcodes_debug.log'));
  	}
  }
  
  protected function _initLanguageLog()
  {
    if (!($this->_logs['language'] instanceof Zend_Log))
    {
      $formatter = new Zend_Log_Formatter_Simple('"%message%";"%message%"' . PHP_EOL);
      $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/radcodes_langauge.txt');
      $writer->setFormatter($formatter);
      
      $this->_logs['language'] = new Zend_Log();
      $this->_logs['language']->addWriter($writer);
    }
  }
  
	public function log($var, $name=null)
	{		
		if( APPLICATION_ENV == 'development' ) {
			
			$this->_initLog();
			
	    if ($name)
	    {
	    	$this->_log->log($name, Zend_Log::DEBUG);
	    }
	    
	    $this->_log->log(print_r($var, true), Zend_Log::WARN);
		}
	}
  
	public function logTranslate($message)
	{
	  
	  /*
	   * put the line below inside Zend_Translate_Adapter::_log()
	   * Engine_Api::_()->getApi('debug','radcodes')->logTranslate($message);
	   */
	  if( APPLICATION_ENV == 'development' ) {
      
      $this->_initLanguageLog();
      $this->_logs['language']->notice($message);
    }
	}
}