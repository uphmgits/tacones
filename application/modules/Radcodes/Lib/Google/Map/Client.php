<?php

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * A class to communicate with Google Maps
 * @author Fabrice Bernhard
 */

class Radcodes_Lib_Google_Map_Client
{
  /**
   * Cache instance
   *
   * @var Radcodes_Lib_Google_Map_Cache
   */
  protected $cache = null;
  
  protected $sensor = false;
  
  const API_URL = 'http://maps.google.com/maps/api/geocode/'; //'http://maps.google.com/maps/geo?';
  const JS_URL  = 'http://maps.google.com/maps/api/js?sensor=true';
  // http://maps.google.com/maps/api/service/output?parameters
    
  /**
   *
   * @author Fabrice Bernhard
   * @since 2009-06-17
   */
  public function __construct()
  {

  }

  
  /**
   * Sets the Sensor
   * @param boolean $value
   */
  public function setSensor($value)
  {
  	$this->sensor = $value;
  	return $this;
  }
  
  /**
   * Gets the Sensor
   * @return boolean $sensor
   */
  public function getSensor()
  {
  	return $this->sensor;
  }
  


  /**
   * Connection to Google Maps' API web service
   *
   * @param string $address
   * @param string $format 'csv' or 'xml'
   * @return string
   * @author fabriceb
   * @since 2009-06-17
   */
  public function getGeocodingInfo($address, $format = 'json', $options = array())
  {
  	$cache_key = $format.$address;
    if ($this->hasCache())
    {
    	if ($this->getCache()->has($cache_key))
    	{
    		$raw_data = $this->getCache()->get($cache_key);
    		return $raw_data;
    	}
    }
    $sensor = $this->sensor ? 'true' : 'false';
    
    $params = array(
      'sensor' => $sensor,
      'address' => $address
    );
    
    $remoteUrl = self::API_URL.$format;
    $client = new Zend_Http_Client($remoteUrl, array('timeout' => 60));
    
    $client->setParameterGet($params);

    $response = $client->request(Zend_Http_Client::GET);
        
    $raw_data = null;
    if ($response->isSuccessful())
    {
      $raw_data = $response->getBody();
    }     

    if ($this->hasCache())
    {
      $this->getCache()->set($cache_key, $raw_data);
    }

    return $raw_data;
  }

  /**
   * Dependency injection for the cache instance
   *
   * @param Radcodes_Lib_Google_Map_Cache $cache
   * @author fabriceb
   * @since 2009-06-17
   */
  public function setCache($cache)
  {
    $this->cache = $cache;
  }

  /**
   *
   * @return Radcodes_Lib_Google_Map_Cache
   * @author fabriceb
   * @since 2009-06-17
   */
  public function getCache()
  {

    return $this->cache;
  }

  /**
   * Is Geocode-Caching to the database enabled?
   * WARNING: this depends on the geocodes caching schema addition
   *
   * @return boolean $hasCache wether the geocodes Table is use to store address lookups
   * @author lukas.schroeder
   * @since 2009-06-09
   * @since 2009-06-17 fabriceb is now using dependency injection and the Radcodes_Lib_Google_Map_Cache bastract class
   */
  public function hasCache()
  {

    return $this->cache instanceof Radcodes_Lib_Google_Map_Cache;
  }

  /**
   * returns the URLS for the google map Javascript file
   * @param boolean $auto_load if the js of Radcodes_Lib_Google_Map should be loaded by default
   * @return string $js_url
   * @author fabriceb
   * @since 2009-06-17
   */
  public function getGoogleJsUrl($auto_load = true)
  {
    $js_url = self::JS_URL;

    return $js_url;
  } 
}
