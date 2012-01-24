<?php

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * GoogleMap Bounds
 * @author Fabrice Bernhard
 * 
 */
class Radcodes_Lib_Google_Map_Coord
{
  /**
   * Latitude
   *
   * @var float
   */
  protected $latitude;
  /**
   * Longitude
   *
   * @var float
   */
  protected $longitude;
  
  const EARTH_RADIUS = 6371; // kilometer
  
  public function __construct($latitude = null, $longitude = null)
  {
    $this->latitude     = floatval($latitude);
    $this->longitude    = floatval($longitude);
  }
  
  
  /**
   * 
   * @param string $lat_col_name
   * @param string $lng_col_name
   * @param float $lat
   * @param float $lng
   * @param Zend_Db_Select $select
   * @return Zend_Db_Select
   * @author fabriceb
   * @since Sep 9, 2009
   */
  public static function criteriaOrderByDistance($lat_col_name, $lng_col_name, $lat, $lng, $select = null)
  {
    if (is_null($select))
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $select = new Zend_Db_Select($db);
    }
    
    $distance_query = '(RADIANS(SQRT(POW(( ABS(%s - %f) ),2) + POW(( ABS(%s - %f) ),2))) * %f)';
    $distance_query = sprintf($distance_query,$lat_col_name, $lat, $lng_col_name, $lng);
        
    $select->columns(array('distance' => $distance_query));
    $select->order('distance');
    
    return $select;
  }
  
  /**
   * 
   * @param string $lat_col_name
   * @param string $lng_col_name
   * @param float $lat
   * @param float $lng
   * @param integer $distance in kms
   * @param Zend_Db_Select $select
   * @param $order_by_distance
   * @return Zend_Db_Select
   * @author maximep
   * @since Sep 9, 2009
   * @since 2009-09-09 fabriceb factorisation
   */
  public static function criteriaInRadius($lat_col_name, $lng_col_name, $lat, $lng, $distance, $select = null, $order_by_distance = false)
  {
    if (is_null($select))
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $select = new Zend_Db_Select($db);
    }
    
    $k = pow(rad2deg($distance/self::EARTH_RADIUS),2);
    
    $distance_query = 'POW(( %s - %f ),2) + POW(( %s - %f ),2) < %f';    
    $distance_query = sprintf($distance_query,$lat_col_name, $lat, $lng_col_name, $lng, $k);
    
    $select->where($distance_query);
    
    if ($order_by_distance)
    {
    	$select = self::criteriaOrderByDistance($lat_col_name,$lng_col_name,$lat,$lng,$select);
    }
    
    return $select;
  }
  
  /**
   * 
   * @param string $lat_col_name
   * @param string $lng_col_name
   * @param integer $distance in kms
   * @param Zend_Db_Select $criteria
   * @param boolean $order_by_distance
   * @return Zend_Db_Select
   * @author maximep
   * @since Sep 9, 2009
   * @since 2009-09-09 fabriceb factorisation
   */
  public function getCriteriaInRadius($lat_col_name, $lng_col_name, $distance, $select = null, $order_by_distance = false)
  {
    return self::criteriaInRadius($lat_col_name, $lng_col_name, $this->getLatitude(), $this->getLongitude(), $distance, $select, $order_by_distance);
  }
  
  public function getLatitude()
  {

    return $this->latitude;
  }
  
  public function getLongitude()
  {
    
    return $this->longitude;
  }
  
  public function setLatitude($latitude)
  {
    $this->latitude = floatval($latitude);
  }
  
  public function setLongitude($longitude)
  {
    $this->longitude = floatval($longitude);
  }
  
  /**
   * 
   * @param $string
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   */
  public static function createFromString($string)
  {
    $coord_array = explode(',',$string);
    if (count($coord_array)==2)
    {
      $latitude = floatval(trim($coord_array[0]));
      $longitude = floatval(trim($coord_array[1]));
      
      return new Radcodes_Lib_Google_Map_Coord($latitude,$longitude);
    }

    return null;
  }
  
  /**
   * 
   * @return string
   */
  public function toJs()
  {
    
    return 'new google.maps.LatLng('.$this->__toString().')';
  }
  
  /**
   * Lng to Pix
   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
   *
   * @param float $lng
   * @param integer $zoom
   * @return integer
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public static function fromLngToPix($lng,$zoom)
  {
    $lngrad = deg2rad($lng);
    $mercx = $lngrad;
    $cartx = $mercx + pi();
    $pixelx = $cartx * 256/(2*pi());
    $pixelx_zoom =  $pixelx * pow(2,$zoom);    
    
    return $pixelx_zoom;
  }
  
  /**
   * Lat to Pix
   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
   *
   * @param float $lat
   * @param integer $zoom
   * @return integer
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public static function fromLatToPix($lat,$zoom)
  {
    if ($lat == 90)
    {
      $pixely = 0;
    }
    else if ($lat == -90)
    {
      $pixely = 256;
    }
    else
    {
      $latrad = deg2rad($lat);
      $mercy = log(tan(pi()/4+$latrad/2));
      $carty = pi() - $mercy;
      $pixely = $carty * 256 / 2 / pi();
      $pixely = max(0, $pixely); // correct rounding errors near north and south poles
      $pixely = min(256, $pixely); // correct rounding errors near north and south poles
    }
    $pixely_zoom = $pixely * pow(2,$zoom);
    
    return $pixely_zoom;
  }
  
  /**
   * Pix to Lng
   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
   *
   * @param integer $pix
   * @param integer $zoom
   * @return float
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public static function fromPixToLng($pixelx_zoom,$zoom)
  {
    $pixelx = $pixelx_zoom / pow(2,$zoom);    
    $cartx = $pixelx / 256 * 2 * pi();    
    $mercx = $cartx - pi();
    $lngrad = $mercx;
    $lng = rad2deg($lngrad);
    
    return $lng;
  }
  
  /**
   * Pix to Lat
   * cf. a World's map according to Google http://mt0.google.com/mt/v=ap.92&hl=en&x=0&y=0&z=0&s=
   *
   * @param integer $pix
   * @param integer $zoom
   * @return float
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public static function fromPixToLat($pixely_zoom,$zoom)
  {    
    $pixely = $pixely_zoom / pow(2,$zoom);
    if ($pixely == 0)
    {
      $lat = 90;
    }
    else if ($pixely == 256)
    {
      $lat = -90;
    }
    else
    {
      $carty = $pixely / 256 * 2 * pi();
      $mercy = pi() - $carty;
      $latrad = 2 * atan(exp($mercy))-pi()/2;
      $lat = rad2deg($latrad);
    }
        
    return $lat;
  }
  
  /**
   * Calculates the center of an array of coordiantes
   * 
   * @param Radcodes_Lib_Google_Map_Coord[] $coords
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   */
  public static function getMassCenterCoord($coords)  
  {
    if (count($coords)==0)
    {
      
      return null;
    }
    $center_lat = 0;
    $center_lng = 0;
    foreach($coords as $coord)
    {
      /* @var $coord Radcodes_Lib_Google_Map_Coord */
      $center_lat += $coord->getLatitude();
      $center_lng += $coord->getLongitude();
    }
  
    return new Radcodes_Lib_Google_Map_Coord($center_lat/count($coords),$center_lng/count($coords));
  }
  
  /**
   * Calculates the center of an array of coordiantes
   * 
   * @param Radcodes_Lib_Google_Map_Coord[] $coords
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   */
  public static function getCenterCoord($coords)  
  {
    $bounds = Radcodes_Lib_Google_Map_Bounds::getBoundsContainingCoords($coords);
  
    return $bounds->getCenterCoord();
  }
  
  /**
   * toString method
   * @return string
   * @author fabriceb
   * @since 2009-05-02
   */
  public function __toString()
  {
    
    return $this->getLatitude().', '.$this->getLongitude();
  }
  
  /**
   * very approximate calculation of the distance in kilometers between two coordinates
   * @param Radcodes_Lib_Google_Map_Coord $coord2
   * @return float
   * @author fabriceb
   * @since 2009-05-03
   */
  public function distanceFrom($coord2)
  {
    $lat_dist = abs($this->getLatitude()-$coord2->getLatitude());
    $lng_dist = abs($this->getLongitude()-$coord2->getLongitude());
    
    $rad_dist = deg2rad(sqrt(pow($lat_dist,2)+pow($lng_dist,2)));
  
    return $rad_dist * self::EARTH_RADIUS;
  }
  
    /**
   * very approximate calculation of the distance in kilometers between two coordinates
   * @param Radcodes_Lib_Google_Map_Coord $coord1
   * @param Radcodes_Lib_Google_Map_Coord $coord2
   * @return float
   * @author fabriceb
   * @since 2009-05-03
   */
  public static function distance($coord1, $coord2)
  {
  
    return $coord1->distanceFrom($coord2);
  }
  
  /**
   * 
   * @param Radcodes_Lib_Google_Map_Bounds $gmap_bounds
   * @return boolean $is_inside
   * @author fabriceb
   * @since Jun 2, 2009 fabriceb
   */
  public function isInsideBounds(Radcodes_Lib_Google_Map_Bounds $gmap_bounds)
  {
  
    return $gmap_bounds->containsCoord($this);
  }
}
