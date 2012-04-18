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
 


class Radcodes_Lib_Rest
{

  const ERROR_SERVER = 'ERROR_SERVER';
  const ERROR_CLIENT = 'ERROR_CLIENT';
  const ERROR_RETRY = 'ERROR_RETRY';	
	
	protected $remoteUrl;
	

	
	protected $_errors = array();
	
	/**
	 * @var Zend_Http_Response
	 */
	protected $_response;
	
	public function __construct()
	{
		$this->remoteUrl = $this->getRemoteUrlFromClass(get_class($this));
	}
	
	
	public function setError($message, $key=null)
	{
	 	if ($key !== null)
	 	{
	 		$this->_errors[] = $message;
	 	}
	 	else
	 	{
	 		$this->_errors[$key] = $message;
	 	}
	}
	
	
	public function getError($key=null)
	{
		if ($key === null) {
			return $this->_errors;
		}
		else {
			return $this->_errors[$key];
		}
	}
	
	
	public function isError()
	{
		return !empty($this->_errors);
	}
	
	
	/**
	 * 
	 * @param string $method Method to execute on server
	 * @param array $params Parameters to for request
	 * @param array $options
	 * 
	 * @return Zend_Http_Response
	 */
	protected function callMethod($method, $params = array(), $options=array())
	{

		if (!isset($options['timeout'])) {
			$options['timeout'] = 60;
		}
		
	  if (!isset($options['format'])) {
      $options['format'] = 'json';
    }
    
		$defaultParams = array(
      'method' => $method,
		  '_servers' => array(
		    'domain' => $_SERVER['SERVER_NAME'],
		    'SERVER_NAME' => $_SERVER['SERVER_NAME'],
		    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
		    'se_version' => 4
		  ),
    );
        
    $params = array_merge($defaultParams, $params);
		
    $client = new Zend_Http_Client($this->remoteUrl, array('timeout' => $options['timeout']));
    $client->setParameterPost($params);
    $this->_response = $response = $client->request(Zend_Http_Client::POST);
    
    if ($response->isSuccessful())
    {
      $responseBody = $response->getBody();

      if (strpos($responseBody, self::ERROR_SERVER) === 0)
      {
        $message = str_replace(self::ERROR_SERVER,'', $responseBody);
        $this->setError($message, self::ERROR_SERVER);
      }
      else if (strpos($responseBody, self::ERROR_RETRY) === 0)
      {
        list($callback, $params) = $this->retryProcess($responseBody);
        return $this->$callback($params);
      }
      else {
      	if ($options['format'] == 'json')
      	{
      		$responseBody = Zend_Json::decode($responseBody);
      	}
      	return $responseBody;
      }
    }     
    else 
    {
    	$this->setError($response->getMessage(), self::ERROR_CLIENT);
    }
    return false;
	}
	
	
	
	
  public function __call($name, $arguments)
  {
  	$classname = get_class($this);
  	trigger_my_error("Calling unsupported method $classname::$name", E_USER_ERROR);
  }
	
  
  protected function retryProcess($message)
  {
  	$strlen_delim = strlen(self::ERROR_RETRY);
  	$message = substr($message, $strlen_delim);
		$start = strpos($message, self::ERROR_RETRY);
		$callback = substr($message, 0, $start);
		$params = substr($message, $start+$strlen_delim);
		return array($callback, $params);
  }
  
  protected function getRemoteUrlFromClass($classname, $tld = '.com')
  {
  	$parts = explode('_', strtolower($classname));
  	$parts[0] .= $tld;
  	return "http://".join('/',$parts).'/';
  }
  
}

