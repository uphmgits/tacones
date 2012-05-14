<?php


class Radcodes_Lib_Google_Map_Cache
{
	/**
	 * 
	 * @var Zend_Cache_Core
	 */
  protected $_cache;
  
  public function __construct()
  {
  	if (Zend_Registry::isRegistered('Zend_Cache'))
  	{
  		$this->_cache = Zend_Registry::get('Zend_Cache');
  	}
  }
  
  protected function _getRealKey($key)
  {
    $key = preg_replace('~[^\\pL\d]+~u', '_', $key);
   
    // trim
    $key = trim($key, '_');

    // lowercase
    $key = strtolower($key);
   
    // remove unwanted characters
    $key = preg_replace('~[^-\w]+~', '_', $key);
  	//echo "KEY=$key=KEY";
  	return get_class($this).'_'.$key;
  }
  
  public function get($key)
  {
  	$key = $this->_getRealKey($key);
  	return $this->_cache->load($key);
  }
  
  public function set($key, $data)
  {
  	$key = $this->_getRealKey($key);
  	$this->_cache->save($data, $key);
  	return $this;
  }
  
  public function clear($key)
  {
  	$key = $this->_getRealKey($key);
  	$this->_cache->remove($key);
  }
  
  public function has($key)
  {
  	$key = $this->_getRealKey($key);
  	return $this->_cache->test($key);
  }
}
