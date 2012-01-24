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
class Radcodes_Lib_Google_Map_Bounds
{
  protected $sw = null;
  protected $ne = null;
  
  /**
   * Create a new Bounds object
   *
   * @param Radcodes_Lib_Google_Map_Coord $nw
   * @param Radcodes_Lib_Google_Map_Coord $se
   */
  public function __construct(Radcodes_Lib_Google_Map_Coord $sw = null, Radcodes_Lib_Google_Map_Coord $ne = null)
  {
    if (is_null($sw))
    {
      $sw = new Radcodes_Lib_Google_Map_Coord();
    }
    if (is_null($ne))
    {
      $ne = new Radcodes_Lib_Google_Map_Coord();
    }
    $this->sw = $sw;
    $this->ne = $ne;
  }
  public function getNorthEast()
  {
    
    return $this->ne;
  }
  
  public function getSouthWest()
  {
    
    return $this->sw;
  }
  
  static public function createFromString($string)
  {
    preg_match('/\(\((.*?)\), \((.*?)\)\)/',$string,$matches);
    if (count($matches)==3)
    {
      $sw = Radcodes_Lib_Google_Map_Coord::createFromString($matches[1]);
      $ne = Radcodes_Lib_Google_Map_Coord::createFromString($matches[2]);
      if ( !is_null($sw) && !is_null($ne))
      {
        
        return new Radcodes_Lib_Google_Map_Bounds($sw,$ne);
      }
      
      return null;
    }
    
    //((48.82415805606007,%202.308330535888672),%20(48.867086142850226,%202.376995086669922))
  }
  
  /**
   * Google String representations
   *
   * @return string
   * @author fabriceb
   * @since Feb 17, 2009 fabriceb
   */
  public function __toString()
  {  

    return '(('.$this->getSouthWest()->getLatitude().', '.$this->getSouthWest()->getLongitude().'), ('.$this->getNorthEast()->getLatitude().', '.$this->getNorthEast()->getLongitude().'))';
  }
  
  /**
   * returns a criteria on two columns to condition on "inside the bounds"
   *
   * @param string $lat_col_name
   * @param string $lng_col_name
   * @param Criteria $criteria
   * @param integer $margin
   * @return Criteria
   * @author fabriceb
   * @since 2008-12-03
   */
  public function criteriaInBounds($lat_col_name, $lng_col_name, $criteria = null, $margin = 0)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }
    
    $lat_tl = $this->getNorthEast()->getLatitude();
    $lat_br = $this->getSouthWest()->getLatitude();
    $lng_tl = $this->getNorthEast()->getLongitude();
    $lng_br = $this->getSouthWest()->getLongitude();
    
    if ($margin!=0)
    {
      $lat_margin = $margin * ($lat_tl-$lat_br);
      $lat_tl -= $lat_margin;
      $lat_br += $lat_margin;
      
      $lng_margin = $margin * ($lng_br-$lng_tl);
      $lng_tl += $lng_margin;
      $lng_br -= $lng_margin;
    }
    
    $sub_query = '%s BETWEEN %F AND %F';
    $lng_subquery = sprintf($sub_query,$lat_col_name, $lat_br, $lat_tl);
    $lat_subquery = sprintf($sub_query,$lng_col_name, $lng_br, $lng_tl);
    
    $criteria->add($lat_col_name,$lat_subquery,CRITERIA::CUSTOM);
    $criteria->add($lng_col_name,$lng_subquery,CRITERIA::CUSTOM);

    return $criteria;
  }
  
  /**
   * Get the latitude of the center of the zone
   *
   * @return integer
   * @author fabriceb
   * @since 2008-12-03 
   */
  public function getCenterLat()
  {
    if (is_null($this->getSouthWest()) || is_null($this->getNorthEast()))
    {
      
      return null;
    }
    
    return floatval(($this->getSouthWest()->getLatitude()+$this->getNorthEast()->getLatitude())/2);
  }
  
   /**
   * Get the longitude of the center of the zone
   *
   * @return integer
   * @author fabriceb
   * @since 2008-12-03 
   */
  public function getCenterLng()
  {
    if (is_null($this->getSouthWest()) || is_null($this->getNorthEast()))
    {
      
      return null; 
    }
    
    return floatval(($this->getSouthWest()->getLongitude()+$this->getNorthEast()->getLongitude())/2);
  }
  
   /**
   * Get the coordinates of the center of the zone
   *
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2008-12-03 
   */
  public function getCenterCoord()
  {
  
    return new Radcodes_Lib_Google_Map_Coord($this->getCenterLat(), $this->getCenterLng());
  }
  
  /**
   * Hauteur du carré
   *
   * @return float
   * @author fabriceb
   * @since Feb 17, 2009 fabriceb
   */
  public function getHeight()
  {
    
    return abs($this->getNorthEast()->getLatitude()-$this->getSouthWest()->getLatitude());
  }
  
  /**
   * Largeur du carré
   *
   * @return float
   * @author fabriceb
   * @since Feb 17, 2009 fabriceb
   */
  public function getWidth()
  {
    
    return abs($this->getNorthEast()->getLongitude()-$this->getSouthWest()->getLongitude());
  }
  
  /**
   * Does a homthety transformtion on the bounds, centered on the center of the bounds
   *
   * @param float $factor
   * @return Radcodes_Lib_Google_Map_Bounds $bounds
   * @author fabriceb
   * @since Feb 17, 2009 fabriceb
   */
  public function getHomothety($factor)
  {
    $bounds = new Radcodes_Lib_Google_Map_Bounds();
    $lat = $this->getCenterLat();
    $lng = $this->getCenterLng();
    $bounds->getNorthEast()->setLatitude($factor*$this->getNorthEast()->getLatitude()+$lat*(1-$factor));
    $bounds->getSouthWest()->setLatitude($factor*$this->getSouthWest()->getLatitude()+$lat*(1-$factor));
    $bounds->getNorthEast()->setLongitude($factor*$this->getNorthEast()->getLongitude()+$lng*(1-$factor));
    $bounds->getSouthWest()->setLongitude($factor*$this->getSouthWest()->getLongitude()+$lng*(1-$factor));
    
    return $bounds;
  }
  
  /**
   * gets zoomed out bounds
   *
   * @param integer $zoom_coef
   * @return Radcodes_Lib_Google_Map_Bounds
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public function getZoomOut($zoom_coef)
  {
    if ($zoom_coef > 0)
    {
      $bounds = $this->getHomothety(pow(2,$zoom_coef));
      
      return $bounds;
    }
    
    return $this;
  }
  
  
  
  /**
   * Returns the most appropriate zoom to see the bounds on a map with min(width,height) = $min_w_h
   *
   * @param integer $min_w_h width or height of the map in pixels
   * @return integer
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public function getZoom($min_w_h, $default_zoom = 14)
  {
    if (!$min_w_h) return $default_zoom;
    
    $infinity = 999999999;
    $factor_h = $infinity;
    $factor_w = $infinity;
    //echo "<br>Google_Map_Bounds min_w_h=$min_w_h default_zom=$default_zoom<br>";
    /*
      
    formula: the width of the bounds in "pixels" is pix_w * 2^z
    We want pix_w * 2^z to fit in min_w_h so we are looking for
    z = round ( log2 ( min_w_h / pix_w  ) )
     */
  
    $sw_lat_pix = Radcodes_Lib_Google_Map_Coord::fromLatToPix($this->getSouthWest()->getLatitude(),0);
    $ne_lat_pix = Radcodes_Lib_Google_Map_Coord::fromLatToPix($this->getNorthEast()->getLatitude(),0);
    $pix_h = abs($sw_lat_pix-$ne_lat_pix);
    if ($pix_h > 0)
    {
      $factor_h = $min_w_h / $pix_h;
    }
    
    $sw_lng_pix = Radcodes_Lib_Google_Map_Coord::fromLngToPix($this->getSouthWest()->getLongitude(),0);
    $ne_lng_pix = Radcodes_Lib_Google_Map_Coord::fromLngToPix($this->getNorthEast()->getLongitude(),0);
    $pix_w = abs($sw_lng_pix-$ne_lng_pix);
    if ($pix_w > 0)
    {
      $factor_w = $min_w_h / $pix_w;
    }
    
    $factor = min($factor_w,$factor_h);
    
    // bounds is one point, no zoom can be determined
    if ($factor == $infinity)
    {
      return $default_zoom;
    }
    
    return round(log($factor,2));
  }
  
  /**
   * Retourne les bounds qui contiennent toutes les autres
   *
   * @param Radcodes_Lib_Google_Map_Bounds[] $boundss
   * @param float $margin
   * @return Radcodes_Lib_Google_Map_Bounds
   * @author fabriceb
   * @since Feb 18, 2009 fabriceb
   */
  public static function getBoundsContainingAllBounds($boundss, $margin = 0)
  {
    $min_lat = 1000;
    $max_lat = -1000;
    $min_lng = 1000;
    $max_lng = -1000;
    foreach($boundss as $bounds)
    {
      $min_lat = min($min_lat, $bounds->getSouthWest()->getLatitude());
      $min_lng = min($min_lng, $bounds->getSouthWest()->getLongitude());
      $max_lat = max($max_lat, $bounds->getNorthEast()->getLatitude());
      $max_lng = max($max_lng, $bounds->getNorthEast()->getLongitude());
    }
    
    if ($margin > 0)
    {
      $min_lat = $min_lat - $margin*($max_lat-$min_lat); 
      $min_lng = $min_lng - $margin*($max_lng-$min_lng);
      $max_lat = $max_lat + $margin*($max_lat-$min_lat); 
      $max_lng = $max_lng + $margin*($max_lng-$min_lng);
    }
    
    $bounds = new Radcodes_Lib_Google_Map_Bounds(new Radcodes_Lib_Google_Map_Coord($min_lat, $min_lng),new Radcodes_Lib_Google_Map_Coord($max_lat, $max_lng));
    return $bounds;
  }
  
  /**
   * Retuns bounds containg an array of coordinates
   *
   * @param Radcodes_Lib_Google_Map_Coord[] $coords
   * @param float $margin
   * @return Radcodes_Lib_Google_Map_Bounds
   * @author fabriceb
   * @since Mar 13, 2009 fabriceb
   */
  public static function getBoundsContainingCoords($coords, $margin = 0)
  {
    $min_lat = 1000;
    $max_lat = -1000;
    $min_lng = 1000;
    $max_lng = -1000;
    foreach($coords as $coord)
    {
      /* @var $coord Radcodes_Lib_Google_Map_Coord */
      $min_lat = min($min_lat, $coord->getLatitude());
      $max_lat = max($max_lat, $coord->getLatitude());
      $min_lng = min($min_lng, $coord->getLongitude());
      $max_lng = max($max_lng, $coord->getLongitude());
    }
    
    if ($margin > 0)
    {
      $min_lat = $min_lat - $margin*($max_lat-$min_lat); 
      $min_lng = $min_lng - $margin*($max_lng-$min_lng);
      $max_lat = $max_lat + $margin*($max_lat-$min_lat); 
      $max_lng = $max_lng + $margin*($max_lng-$min_lng);
    }
    $bounds = new Radcodes_Lib_Google_Map_Bounds(new Radcodes_Lib_Google_Map_Coord($min_lat, $min_lng),new Radcodes_Lib_Google_Map_Coord($max_lat, $max_lng));
    
    return $bounds;
  }
  
  
  /**
  *
  * @param Radcodes_Lib_Google_Map_Marker[] $markers array of MArkers
  * @param float $margin margin factor for the bounds
  * @return Radcodes_Lib_Google_Map_Bounds
  * @author fabriceb
  * @since 2009-05-02
  *
  **/
  public static function getBoundsContainingMarkers($markers, $margin = 0)
  {
    $coords = array();
    foreach($markers as $marker)
    {
      array_push($coords, $marker->getCoord());
    }
   
    return Radcodes_Lib_Google_Map_Bounds::getBoundsContainingCoords($coords, $margin);
  }
  
  
  /**
   * Calculate the bounds corresponding to a specific center and zoom level for a give map size in pixels
   * 
   * @param Radcodes_Lib_Google_Map_Coord $center_coord
   * @param integer $zoom
   * @param integer $width
   * @param integer $height
   * @return Radcodes_Lib_Google_Map_Bounds
   * @author fabriceb
   * @since Jun 2, 2009 fabriceb
   */
  public static function getBoundsFromCenterAndZoom(Radcodes_Lib_Google_Map_Coord $center_coord, $zoom, $width, $height = null)
  {
    if (is_null($height))
    {
      $height = $width;
    }
    
    $center_lat = $center_coord->getLatitude();
    $center_lng = $center_coord->getLongitude();

    $pix = Radcodes_Lib_Google_Map_Coord::fromLatToPix($center_lat, $zoom);
    $ne_lat = Radcodes_Lib_Google_Map_Coord::fromPixToLat($pix - round(($height-1) / 2), $zoom);
    $sw_lat = Radcodes_Lib_Google_Map_Coord::fromPixToLat($pix + round(($height-1) / 2), $zoom);
    
    $pix = Radcodes_Lib_Google_Map_Coord::fromLngToPix($center_lng, $zoom);
    $sw_lng = Radcodes_Lib_Google_Map_Coord::fromPixToLng($pix - round(($width-1) / 2), $zoom);
    $ne_lng = Radcodes_Lib_Google_Map_Coord::fromPixToLng($pix + round(($width-1) / 2), $zoom);

    return new Radcodes_Lib_Google_Map_Bounds(new Radcodes_Lib_Google_Map_Coord($sw_lat, $sw_lng), new Radcodes_Lib_Google_Map_Coord($ne_lat, $ne_lng));
  }
  
  /**
   * 
   * @param Radcodes_Lib_Google_Map_Coord $gmap_coord
   * @return boolean $is_inside
   * @author fabriceb
   * @since Jun 2, 2009 fabriceb
   */
  public function containsCoord(Radcodes_Lib_Google_Map_Coord $gmap_coord)
  {
    $is_inside = 
      (
      $gmap_coord->getLatitude() < $this->getNorthEast()->getLatitude()
      &&
      $gmap_coord->getLatitude() > $this->getSouthWest()->getLatitude()
      &&
      $gmap_coord->getLongitude() < $this->getNorthEast()->getLongitude()
      &&
      $gmap_coord->getLongitude() > $this->getSouthWest()->getLongitude()
      );
  
    return $is_inside;
  }
  
  
}
