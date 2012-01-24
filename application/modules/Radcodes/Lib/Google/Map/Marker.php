<?php

/*
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * A GoogleMap Marker
 * @author Fabrice Bernhard
 * 
 */
class Radcodes_Lib_Google_Map_Marker
{
   /**
   * javascript name of the marker
   *
   * @var string
   */
  protected $js_name        = null;
  protected $options = array(
    //  Map  Map on which to display Marker.  
    'map ' => null,
    // LatLng  Marker position. Required.  
    'position ' => null,
    // string  Rollover text  
    'title ' => null,
    // Icon  for the foreground  
    'icon ' => null,
    // Shadow  image  
    'shadow ' => null,
    // Object  Image map region for drag/click. Array of x/y values that define the perimeter of the icon.  
    'shape ' => null,
    // string  Mouse cursor to show on hover  
    'cursor ' => null,
    // boolean  If true, the marker can be clicked  
    'clickable ' => null,
    // boolean  If true, the marker can be dragged.  
    'draggable ' => null,
    // boolean  If true, the marker is visible  
    'visible ' => null,
    // boolean  If true, the marker shadow will not be displayed.  
    'flat ' => null,
    // number  All Markers are displayed on the map in order of their zIndex, with higher values displaying in front of Markers with lower values. By default, Markers are displayed according to their latitude, with Markers of lower latitudes appearing in front of Markers at higher latitudes.  
    'zIndex ' => null,
  );
  protected $info_window = null;
  protected $shadow           = null;
  protected $events         = array();
  protected $custom_properties = array();
  
  /**
   * @param string $js_name Javascript name of the marker
   * @param float $lat Latitude
   * @param float $lng Longitude
   * @param Radcodes_Lib_Google_Map_Icon $icon
   * @param GmapEvent[] array of GoogleMap Events linked to the marker
   * @author Fabrice Bernhard
   */
  public function __construct($lat,$lng,$options = array(),$js_name='marker',$events=array())
  {
    $this->js_name = $js_name;
    $this->setOptions($options);
    $this->setCoord(new Radcodes_Lib_Google_Map_Coord($lat,$lng));
    $this->events  = $events;    
  }
  
  /**
   * Construct from a Radcodes_Lib_Google_Map_GeocodedAddress object
   *
   * @param string $js_name
   * @param Radcodes_Lib_Google_Map_GeocodedAddress $gmap_geocoded_address
   * @return Radcodes_Lib_Google_Map_Marker
   */
  public static function constructFromGeocodedAddress($gmap_geocoded_address,$js_name='marker')
  {
    if (!$gmap_geocoded_address instanceof Radcodes_Lib_Google_Map_GeocodedAddress)
    {
      throw new Radcodes_Lib_Google_Map_Exception('object passed to constructFromGeocodedAddress is not a Radcodes_Lib_Google_Map_GeocodedAddress');
    }
    
    return new Radcodes_Lib_Google_Map_Marker($js_name,$gmap_geocoded_address->getLat(),$gmap_geocoded_address->getLng());
  }
  
  /**
  * @return string $js_name Javascript name of the marker  
  */
  public function getName()
  {
    
    return $this->js_name;
  }
  /**    
  * @return Radcodes_Lib_Google_Map_Icon $icon  
  */
  public function getIcon()
  {
    return null;//return $this->icon;
  }
  /**    
  * @return Radcodes_Lib_Google_Map_Icon $icon  
  */
  public function getShadow()
  {
    return $this->shadow;
  }
  /**
   * @param array $options
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setOptions($options)
  {
    $this->options = array_merge($this->options,$options);
  }
  /**
   * @return array $options
   * @author fabriceb
   * @since 2009-08-21
   */
  public function getOptions()
  {

    return $this->options;
  }
    /**
   * 
   * @param string $name
   * @return mixed
   * @author fabriceb
   * @since 2009-08-21
   */
  public function getOption($name)
  {
    
    return $this->options[$name];
  }
  
  /**
   * 
   * @param string $name
   * @param mixed $value
   * @return void
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;
  }
  
  /**
   * returns the coordinates object of the marker
   * 
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   * @since 2009-08-21
   */
  public function getCoord()
  {
  
    return $this->getOption('position');
  }
  /**
   * sets the coordinates object of the marker
   * 
   * @param Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   * @since 2009-08-21
   */
  public function setCoord($gmap_coord)
  {
    $this->setOption('position', $gmap_coord);
  }  
  /**
  * @return float $lat Javascript latitude  
  */
  public function getLat()
  {
    
    return $this->getCoord()->getLatitude();
  }
  /**
  * @return float $lng Javascript longitude  
  */
  public function getLng()
  {
    
    return $this->getCoord()->getLongitude();
  }
    
  public function getEvents()
  {
    return $this->events;
  }
  
  public function getEvent($key)
  {
    return isset($this->events[$key]) ? $this->events[$key] : null;
  }
  /**
   * 
   * @return string
   * @author fabriceb
   * @since 2009-08-21
   */
  public function optionsToJs()
  {
    $options_array = array();
    foreach($this->options as $name => $value)
    {
      if (!is_null($value))
      {
        switch($name)
        {
          case 'position':
          case 'icon':
          case 'shadow':
            $options_array[] = $name.': '.$value->toJs();
            break;
          case 'title':
          	$value = '"' . str_replace('"','\"',$value) . '"';
          	$options_array[] = $name.': '.$value;
          	break;
          default:
            $options_array[] = $name.': '.$value;
            break;
        }
      }
    }
    $tab = '  ';
    $separator = "\n".$tab.$tab;
    
    return '{'.$separator.$tab.implode(','.$separator.$tab, $options_array).$separator.'}';
  }
  
  /**
  * @param string $map_js_name 
  * @return string Javascript code to create the marker
  * @author Fabrice Bernhard
  * @since 2009-08-21
  */
  public function toJs($map_js_name = 'map')
  {
    $this->setOption('map', $map_js_name);
    if ($this->getIcon() instanceof Radcodes_Lib_Google_Map_MarkerImage)
    {
      $this->setOption('icon', $this->getIcon());
    }
    if ($this->getShadow() instanceof Radcodes_Lib_Google_Map_MarkerImage)
    {
      $this->setOption('shadow', $this->getShadow());
    }
    
    $return = '';
    if($this->info_window instanceof Radcodes_Lib_Google_Map_InfoWindow)
    {
      $this->addEvent(new Radcodes_Lib_Google_Map_Event('click',$this->info_window->getName().".open(".$map_js_name.",".$this->getName().");"));
      $return .= $this->info_window->toJs();
    }
    $return .= '    '.$this->getName().' = new google.maps.Marker('.$this->optionsToJs().");\n";
    foreach ($this->custom_properties as $attribute=>$value)
    {
      $return .= $this->getName().".".$attribute." = '".$value."';";
    }
    foreach ($this->events as $event)
    {
      $return .= '    '.$event->getEventJs($this->getName())."\n";
    }   
    
    return $return;
  }
  
  /**
   * Adds an event listener to the marker
   *
   * @param Radcodes_Lib_Google_Map_Event $event
   */
  public function addEvent($event, $key=null)
  {
    if ($key === null) {
      array_push($this->events,$event);
    }
    else {
      $this->events[$key] = $event;
    }
  }
  /**
   * Adds an onlick listener that open a html window with some text 
   *
   * @param G
   * @author fabriceb
   * @since Feb 20, 2009 fabriceb removed the escape_javascript function which made the plugin incompatible with symfony 1.2
   * 
   * @author Maxime Picaud
   * @since 7 sept. 20009 Modified to correspond with new Api v3
   */
  public function addHtmlInfoWindow(Radcodes_Lib_Google_Map_InfoWindow $info_window)
  {
    $this->info_window = $info_window;
  }
  
  /**
  * @return Radcodes_Lib_Google_Map_InfoWindow
  * @author fabriceb
  * @since Oct 13, 2009
  */
  public function getHtmlInfoWindow()
  {
  
    return $this->info_window;
  }
  
  /**
   * Returns the code for the static version of Google Maps
   * @TODO Add support for color and alpha-char
   * @author Laurent Bachelier
   * @return string
   */
  public function getMarkerStatic()
  {
    
    return $this->getLat().','.$this->getLng();
  }
  public function setCustomProperties($custom_properties)
  {
    $this->custom_properties=$custom_properties;
  }
  public function getCustomProperties()
  {
    
    return $this->custom_properties;
  }
  /**
   * Sets a custom property to the generated javascript object
   *
   * @param string $name
   * @param string $value
   */
  public function setCustomProperty($name,$value)
  {
    $this->custom_properties[$name] = $value;
  }
  
  /**
  *
  * @param Radcodes_Lib_Google_Map_Marker[] $markers array of MArkers
  * @return Radcodes_Lib_Google_Map_Coord
  * @author fabriceb
  * @since 2009-05-02
  *
  **/
  public static function getMassCenterCoord($markers)
  {
    $coords = array();
    foreach($markers as $marker)
    {
      array_push($coords, $marker->getCoord());
    }
   
    return Radcodes_Lib_Google_Map_Coord::getMassCenterCoord($coords);
  }
  
  /**
  *
  * @param Radcodes_Lib_Google_Map_Marker[] $markers array of MArkers
  * @return Radcodes_Lib_Google_Map_Coord
  * @author fabriceb
  * @since 2009-05-02
  *
  **/
  public static function getCenterCoord($markers)
  {
    $bounds = Radcodes_Lib_Google_Map_Bounds::getBoundsContainingMarkers($markers);
  
    return $bounds->getCenterCoord();
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
  
    return $this->getCoord()->isInsideBounds($gmap_bounds);
  }
  
}
