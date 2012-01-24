<?php

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * A class to geocode addresses
 * @author Fabrice Bernhard
 */

class Radcodes_Lib_Google_Map_GeocodedAddress
{
	const TYPE_COUNTRY     = 'country';
	const TYPE_PROVINCE    = 'administrative_area_level_1';
	const TYPE_CITY        = 'locality';
	
  protected $raw_address           = null;
  protected $formatted_address     = null;
  protected $lat                   = null;
  protected $lng                   = null;
  protected $accuracy              = null;
  
  protected $city           = null;
  protected $country_code   = null;
  protected $country        = null;
  protected $route          = null;
  protected $street_number  = null;
  protected $subpremise     = null;
  protected $premise        = null;
  protected $street_address = null;
  protected $postal_code    = null;
  protected $province       = null;
  protected $province_code  = null;
  protected $county         = null;
  protected $neighborhood   = null;
  protected $types          = null;
  
  /**
   * @var Radcodes_Lib_Google_Map_Coord
   */
  protected $coord          = null;
  
  /**
   * Constructs a gMapGeocodedAddress object from a given $raw_address String
   *
   * @param string $raw_address
   * @author Fabrice Bernhard
   */
  public function __construct($raw_address)
  {
    $this->raw_address = $raw_address;
  }

  /**
   *
   * @return string $raw_address
   * @author fabriceb
   * @since 2009-06-17
   */
  public function getRawAddress()
  {
    return $this->raw_address;
  }



  /**
   * Geocodes the address using the Google Maps  webservice
   *
   * @param Radcodes_Lib_Google_Map_Client $gmap_client
   * @return boolean $accuracy
   * @author Fabrice Bernhard
   * @author Vincent Van
   */
  public function geocode($gmap_client, $format='json')
  {
  	if ($format == 'xml') {
  		$result = $this->geocodeXml($gmap_client);
  	}
  	else {
  		$result = $this->geocodeJson($gmap_client);
  	}
    return $result;
  }

  /**
   * Geocodes the address using the Google Maps XML webservice, which has more information.
   * Unknown values will be set to NULL.
   * @param Radcodes_Lib_Google_Map_Client $gmap_client
   * @return integer $accuracy
   * @author Fabrice Bernhard
   */
  public function geocodeXml($gmap_client)
  {
    
  }


  /**
   * Geocodes the address using the Google Maps  webservice
   *
   * @param Radcodes_Lib_Google_Map_Client $gmap_client
   * @return integer $accuracy
   * @author Fabrice Bernhard
   * @author Vincent Van
   */
  public function geocodeJson($gmap_client)
  {
    $raw_data = utf8_encode($gmap_client->getGeocodingInfo($this->getRawAddress(),'json'));

    if (empty($raw_data))
    {
    	return false;
    }
    
    $values = Zend_Json::decode($raw_data);
    
    if (!is_array($values) || $values['status'] != 'OK')
    {
    	return false;
    }
        
    $result = $values['results'][0];
    $address_components = $result['address_components'];
    
    $this->lng = $result['geometry']['location']['lng'];
    $this->lat = $result['geometry']['location']['lat'];
    
    @$this->formatted_address = $result['formatted_address'];
    $this->types = isset($result['types']) ? $result['types'] : array();
    $this->coord = new Radcodes_Lib_Google_Map_Coord($this->lat, $this->lng);
    
    $this->street_number = $this->extractAddressComponent($address_components, 'street_number');
    $this->route = $this->extractAddressComponent($address_components, 'route');
    $this->subpremise = $this->extractAddressComponent($address_components, 'subpremise');
    $this->premise = $this->extractAddressComponent($address_components, 'premise');
    
    $this->street_address = trim("$this->street_number $this->route $this->subpremise");
    
    $this->city = $this->extractAddressComponent($address_components, 'locality');

    $this->province = $this->extractAddressComponent($address_components, 'administrative_area_level_1');
    $this->province_code = $this->extractAddressComponent($address_components, 'administrative_area_level_1', 'short_name');
    $this->country = $this->extractAddressComponent($address_components, 'country');
    $this->country_code = $this->extractAddressComponent($address_components, 'country', 'short_name');
    $this->postal_code = $this->extractAddressComponent($address_components,'postal_code');
    
    $this->neighborhood = $this->extractAddressComponent($address_components, 'neighborhood');
    $this->county = $this->extractAddressComponent($address_components, 'administrative_area_level_2');

    return true;
  }
  
  
  /**
   * Returns the latitude
   * @return float $latitude
   */
  public function getLat()
  {
    return $this->lat;
  }

  /**
   * Returns the longitude
   * @return float $longitude
   */
  public function getLng()
  {
    return $this->lng;
  }

  /**
   * Returns the Geocoding accuracy
   * @return integer $accuracy
   */
  public function getAccuracy()
  {

    return $this->accuracy;
  }

  /**
   * Returns the address normalized by the Google Maps web service
   * @return string $route
   */
  public function getRoute()
  {
    return $this->route;
  }

  /**
   * Returns the address containing the human-readable address of this location by the Google Maps web service
   * @return string $formatted_address
   */  
  public function getFormattedAddress()
  {
  	return $this->formatted_address;
  }
  
  /**
   * Returns the city normalized by the Google Maps web service
   * @return string $city
   */
  public function getCity()
  {

    return $this->city;
  }

  /**
   * Returns the province code normalized by the Google Maps web service
   * @return string $province
   */
  public function getProvinceCode()
  {
    return $this->province_code;
  }  
  
  /**
   * Returns the province normalized by the Google Maps web service
   * @return string $province
   */
  public function getProvince()
  {
    return $this->province;
  }    
  
  /**
   * Returns the country code normalized by the Google Maps web service
   * @return string $country_code
   */
  public function getCountryCode()
  {

    return $this->country_code;
  }

  /**
   * Returns the country normalized by the Google Maps web service
   * @return string $country
   */
  public function getCountry()
  {

    return $this->country;
  }

  /**
   * Returns the postal code normalized by the Google Maps web service
   * @return string $postal_code
   */
  public function getPostalCode()
  {
    return $this->postal_code;
  }

  /**
   * Returns the street name normalized by the Google Maps web service
   * @return string $street_number
   */
  public function getStreetNumber()
  {
    return $this->street_number;
  }

  /**
   * Returns indicates a first-order entity below a named location, 
   * usually a singular building within a collection of buildings with a common name,
   * or a apartment unit, a suite, room number
   * @return string $subpremise
   */
  public function getSubpremise()
  {
  	return $this->subpremise;
  }
  
  /**
   * Returns indicates a named location, usually a building or collection of buildings with a common name
   * @return string $premise
   */  
  public function getPremise()
  {
  	return $this->premise;
  }
  
  /**
   * Returns indicates a second-order civil entity below the country level. 
   * Within the United States, these administrative levels are counties. 
   * Not all nations exhibit these administrative levels.
   * @return string $county
   */
  public function getCounty()
  {
  	return $this->county;
  }
  
  /**
   * Returns indicates a named neighborhood
   * @return string $neighborhood
   */  
  public function getNeighborhood()
  {
  	return $this->neighborhood;
  }
  
  /**
   * Returns street address, usually combined street number + route + subpremise
   * @return string $street_address
   */
  public function getStreetAddress()
  {
  	return $this->street_address;
  }
  
  /**
   * @param string $raw raw address to set
   */
  public function setRawAddress($raw)
  {
    $this->raw_address = $raw;
    return $this;
  }

  /**
   * @param string $formatted formatted address to set
   */
  public function setFormattedAddress($formatted)
  {
  	$this->formatted_address = $formatted;
  	return $this;
  }
  
  /**
   * @param string $street_address street address to set
   */
  public function setStreetAddress($street_address)
  {
  	$this->street_address = $street_address;
  	return $this;
  }
  
  /**
   * @param string $neighborhood neighborhood to set
   */
  public function setNeighborhood($neighborhood)
  {
    $this->neighborhood = $neighborhood;
    return $this;
  }
  
  /**
   * @param string $subpremise subpremise to set
   */
  public function setSubpremise($subpremise)
  {
    $this->subpremise = $subpremise;
    return $this;
  }
  
  /**
   * @param string $premise premise to set
   */
  public function setPremise($premise)
  {
    $this->premise = $premise;
    return $this;
  }
  
  /**
   * @param string $county county to set
   */
  public function setCounty($county)
  {
    $this->county = $county;
    return $this;
  }  
  
  /**
   * @param float $lat latitude to set
   */
  public function setLat($lat)
  {
    $this->lat = $lat;
    return $this;
  }

  /**
   * @param float $lat longitude to set
   */
  public function setLng($lng)
  {
    $this->lng = $lng;
    return $this;
  }

  /**
   * @param float $lat accuracy to set
   */
  public function setAccuracy($accuracy)
  {
    $this->accuracy = $accuracy;
    return $this;
  }

  /**
   * @param string $val geocoded city
   */
  public function setCity($val)
  {
    $this->city = $val;
    return $this;
  }

  /**
   * @param Radcodes_Lib_Google_Map_Coord $val
   */
  public function setCoord($val)
  {
  	$this->coord = $val;
  	if ($this->coord instanceof Radcodes_Lib_Google_Map_Coord)
  	{
  		$this->lat = $this->coord->getLatitude();
  		$this->lng = $this->coord->getLongitude();
  	}
  	return $this;
  }
  
  /**
   * @param string $val geocoded province code
   */
  public function setProvinceCode($val)
  {
    $this->province_code = $val;
    return $this;
  }  
  
  /**
   * @param string $val geocoded province
   */
  public function setProvince($val)
  {
    $this->province = $val;
    return $this;
  }    
  
  /**
   * @param string $val geocoded country code
   */
  public function setCountryCode($val)
  {
    $this->country_code = $val;
    return $this;
  }

  /**
   * @param string $val geocoded country
   */
  public function setCountry($val)
  {
    $this->country = $val;
    return $this;
  }

  /**
   * @param string $val geocoded route
   */
  public function setRoute($val)
  {
    $this->route = $val;
    return $this;
  }

  /**
   * @param string $val geocoded street
   */
  public function setStreetNumber($val)
  {
    $this->street_number = $val;
    return $this;
  }

  /**
   * @param string $val geocoded postal_code
   */
  public function setPostalCode($val)
  {
    $this->postal_code = $val;
    return $this;
  }


  protected function extractAddressComponent($components, $type, $format='long_name')
  {
  	foreach ($components as $component)
  	{
  		if ($component['types'][0] == $type)
  		{
  			return $component[$format];
  		}
  	}
  	return null;
  }
  
  public function hasType($type)
  {
  	return in_array($type, $this->types);
  }
  
  /**
   * @return Radcodes_Lib_Google_Map_Coord
   */
  public function getCoord()
  {
  	if (null === $this->coord)
  	{
  		$this->coord = new Radcodes_Lib_Google_Map_Coord($this->lat, $this->lng);
  	}
  	return $this->coord;
  }
}
