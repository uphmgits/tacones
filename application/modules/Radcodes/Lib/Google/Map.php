<?php

/*
GMapMarkerImage => Radcodes_Lib_Google_Map_MarkerImage
GMapMarker => Radcodes_Lib_Google_Map_Marker
GMapInfoWindow => Radcodes_Lib_Google_Map_InfoWindow
GMapIcon => Radcodes_Lib_Google_Map_Icon
GMapGeocodedAddress => Radcodes_Lib_Google_Map_GeocodedAddress
GMapEvent => Radcodes_Lib_Google_Map_Event
GMapDirection => Radcodes_Lib_Google_Map_Direction
GMapDirectionWaypoint => Radcodes_Lib_Google_Map_DirectionWaypoint
GMapCoord => Radcodes_Lib_Google_Map_Coord
GMapClientTestCache => Radcodes_Lib_Google_Map_ClientTestCache
GMapClient => Radcodes_Lib_Google_Map_Client
GMapBounds => Radcodes_Lib_Google_Map_Bounds
GMap => Radcodes_Lib_Google_Map

sfException => Radcodes_Lib_Google_Map_Exception
sfConfig => Radcodes_Lib_Google_Map_Config
sfCache => Radcodes_Lib_Google_Map_Cache
 */

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * @author Fabrice Bernhard
 *
 */

class Radcodes_Lib_Google_Map
{

  protected $options = array(
    // boolean  If true, do not clear the contents of the Map div.  
    'noClear ' => null,
    // string Color used for the background of the Map div. This color will be visible when tiles have not yet loaded as a user pans.  
    'backgroundColor' => null,
    // string The name or url of the cursor to display on a draggable object.  
    'draggableCursor' => null,
    // string The name or url of the cursor to display when an object is dragging.  
    'draggingCursor' => null,
    // boolean If false, prevents the map from being dragged. Dragging is enabled by default.  
    'draggable' => null,
    // boolean If true, enables scrollwheel zooming on the map. The scrollwheel is disabled by default.  
    'scrollwheel' => null,
    // boolean If false, prevents the map from being controlled by the keyboard. Keyboard shortcuts are enabled by default.  
    'keyboardShortcuts' => null,
    // LatLng The initial Map center. Required.  
    'center' => null,
    // number The initial Map zoom level. Required.  
    'zoom' => null,
    // string The initial Map mapTypeId. Required.  
    'mapTypeId' => 'google.maps.MapTypeId.ROADMAP',
    // boolean Enables/disables all default UI. May be overridden individually.  
    'disableDefaultUI' => null,
    // boolean The initial enabled/disabled state of the Map type control.  
    'mapTypeControl' => null,
    // MapTypeControl options The initial display options for the Map type control.  
    'mapTypeControlOptions' => null,
    // boolean The initial enabled/disabled state of the scale control.  
    'scaleControl' => null,
    // ScaleControl options The initial display options for the scale control.  
    'scaleControlOptions' => null,
    // boolean The initial enabled/disabled state of the navigation control.  
    'navigationControl' => null,
    // NavigationControl options The initial display options for the navigation control.  
    'navigationControlOptions' => null
  );
  
  protected $parameters = array(
      'js_name' => 'radcodes_google_map',
      'onload_method' => 'mootools',
      'link_to_infowindow' => false
  );

  // id of the Google Map div container
  protected $container_attributes = array(
      'id' =>'radcodes_google_map_container'
  );
  
  // style of the container
  protected $container_style=array(
    'width'=>'512px',
    'height'=>'512px'
  );

  // objects linked to the map
  protected $icons=array();
  protected $markers=array();
  protected $events=array();
  protected $directions=array();

  // customise the javascript generated
  protected $after_init_js=array();
  protected $global_variables=array();

  // the interface to the Google Maps API web service
  protected $gMapClient = false;  

  /**
   * Constructs a Google Map PHP object
   *
   * @param array $options
   * @param array $attributes
   */
  public function __construct($options=array(), $container_style=array(), $container_attributes=array(), $parameters=array())
  {
    $this->setOptions($options);
    $this->setContainerAttributes($container_attributes);
    $this->setContainerStyles($container_style);
    $this->setParameters($parameters);
    
    // delcare the Google Map Javascript object as global
    $this->addGlobalVariable($this->getJsName(),'null');

  }
  
  /**
   * @param string $name
   * @return Radcodes_Lib_Google_Map
   */
  static public function getInstance($name = 'default', $options=array(), $container_style=array(), $container_attributes=array(), $parameters=array())
  {
  	static $maps = array();
  	
  	if (!isset($maps[$name]))
  	{
	    $parameters = array_merge(array('js_name' => 'radcodes_google_map_'.$name), $parameters);
	    $container_attributes = array_merge(array('id' => 'radcodes_google_map_container_'.$name), $container_attributes);
	    
	    $maps[$name] = new Radcodes_Lib_Google_Map($options, $container_style, $container_attributes, $parameters);
  	}
  	

  	return $maps[$name];
  }
  

  
  /**
   * Defines the style of the Google Map div
   * @param array $style Associative array with the style of the div container
   */
  public function setContainerStyles($container_style)
  {
    $this->container_style = array_merge($this->container_style,$container_style);
    return $this;
  }
  /**
   * Gets the style Array of the div container
   */
  public function getContainerStyles()
  {
    return $this->container_style;
  }
  /**
   * Defines the attributes of the Google Map div
   * @param array $container_attributes Associative array with the attributes of the div container
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setContainerAttributes($container_attributes)
  {
    $this->container_attributes = array_merge($this->container_attributes,$container_attributes);
    return $this;
  }
  /**
   * Gets the attributes array of the div container
   * @author fabriceb
   * @since 2009-08-21
   */
  public function getContainerAttributes()
  {

    return $this->container_attributes;
  }
  /**
   * @param array $options
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setOptions($options)
  {
    $this->options = array_merge($this->options,$options);
    return $this;
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
   * @param array $parameters
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setParameters($parameters)
  {
    $this->parameters = array_merge($this->parameters,$parameters);
    return $this;
  }
  /**
   * @return array $parameters
   * @author fabriceb
   * @since 2009-08-21
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * @param string $name
   * @param mixed $value
   * @author fabriceb
   * @since 2009-08-21
   */
  public function setParameter($name, $value)
  {
    $this->parameters[$name] = $value;
    return $this;
  }
  /**
   * @return mixed $value
   * @author fabriceb
   * @since 2009-08-21
   */
  public function getParameter($name)
  {
    return $this->parameters[$name];
  }
  /**
   * gets an instance of the interface to the Google Map web geocoding service
   *
   * @return Radcodes_Lib_Google_Map_Client
   * @author fabriceb
   * @since 2009-06-17
   */
  public function getClient()
  {
    if ($this->gMapClient === false)
    {
      $this->gMapClient = new Radcodes_Lib_Google_Map_Client();
      
      if( Zend_Registry::isRegistered('Zend_Cache') &&
        ($cache = Zend_Registry::get('Zend_Cache')) instanceof Zend_Cache_Core &&
        $cache->getOption('caching') ) {

        	$map_cache = new Radcodes_Lib_Google_Map_Cache();
        	$this->gMapClient->setCache($map_cache);
      }      
    }

    return $this->gMapClient;
  }

  /**
   * sets an instance of the interface to the Google Map web geocoding service
   *
   * @param Radcodes_Lib_Google_Map_Client
   * @author fabriceb
   * @since 2009-06-17
   */
  public function setClient($gMapClient)
  {
    $this->gMapClient = $gMapClient;
    return $this;
  }


  /**
   * Geocodes an address
   * @param string $address
   * @return Radcodes_Lib_Google_Map_GeocodedAddress
   * @author Fabrice Bernhard
   */
  public function geocode($address, $format='json')
  {
    $address = trim($address);

    $gMapGeocodedAddress = new Radcodes_Lib_Google_Map_GeocodedAddress($address);
    $accuracy = $gMapGeocodedAddress->geocode($this->getClient(), $format);

    if ($accuracy)
    {
      return $gMapGeocodedAddress;
    }

    return null;
  }

  
  /**
   * @return string $this->options['js_name'] Javascript name of the googlemap
   */
  public function getJsName()
  {

    return $this->parameters['js_name'];
  }

  /**
   * Defines one style of the div container
   * @param string $style_tag name of css tag
   * @param string $style_value value of css tag
   */
  public function setContainerStyle($style_tag,$style_value)
  {
    $this->container_style[$style_tag]=$style_value;
    return $this;
  }
  /*
   * Gets one style of the Google Map div
   * @param string $style_tag name of css tag
   */
  public function getContainerStyle($style_tag)
  {

    return $this->container_style[$style_tag];
  }

  public function getContainerId()
  {

    return $this->container_attributes['id'];
  }

  /**
   * returns the Html for the Google map container
   * @param Array $options Style options of the HTML container
   * @return string $container
   * @author Fabrice Bernhard
   */
  public function getContainer($styles=array(),$attributes=array())
  {
    $this->container_style = array_merge($this->container_style,$styles);
    $this->container_attributes = array_merge($this->container_attributes,$attributes);

    $style="";
    foreach ($this->container_style as $tag=>$val)
    {
      $style.=$tag.":".$val.";";
    }

    $attributes = $this->container_attributes;
    $attributes['style'] = $style;

    return Radcodes_Lib_Google_Map_RenderTag::renderContent('div',null,$attributes);
  }
  
  /**
   * 
   * @return string
   * @author fabriceb
   * @since 2009-08-20
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
          case 'navigationControlOptions':
          case 'scaleControlOptions':
          case 'mapTypeControlOptions':
            $options_array[] = $name.': {style: '.$value.'}';
            break;
          case 'center':
            $options_array[] = $name.': '.$value->toJs();
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
   * 
   * @return unknown_type
   * @author fabriceb
   * @since Oct 8, 2009
   */
  public function getOnloadJs()
  {
  	$init_name = $this->getInitializeJsFunctionName();
    switch ($this->parameters['onload_method'])
    {
      case 'jQuery':
        return 'jQuery(document).ready(function(){'.$init_name.'();});';
        break;
      case 'prototype':
        return 'document.observe("dom:loaded", function(){'.$init_name.'();});';
        break;
      case 'mootools':
      	return 'window.addEvent("load", function(){'.$init_name.'();});';
      	break;  
      default:
      case 'js':
        return 'window.onload = function(){'.$init_name.'()};';
        break;
    }
  }

  public function getInitializeJsFunctionName()
  {
  	$name = $this->getJsName().'_initialize';
  	return $name;
  }
  
  /**
   * Returns the Javascript for the Google map
   * @param Array $options
   * @return $string
   * @author Fabrice Bernhard
   * @since 2009-08-21 fabriceb v3
   */
  public function getJavascript()
  {

    $return ='';
    $init_events = array();
    $init_events[] = 'var mapOptions = '.$this->optionsToJs().';';
    $init_events[] = $this->getJsName().' = new google.maps.Map(document.getElementById("'.$this->getContainerId().'"), mapOptions);';

    // add some more events
    $init_events[] = $this->getEventsJs();
    $init_events[] = $this->getIconsJs();
    $init_events[] = $this->getMarkersJs();
    $init_events[] = $this->getDirectionsJs();
    foreach ($this->after_init_js as $after_init)
    {
      $init_events[] = $after_init;
    }

    foreach($this->global_variables as $name=>$value)
    {
      $return .= '
  var '.$name.' = '.$value.';';
    }
    
    $init_name = $this->getInitializeJsFunctionName();
    
    $return .= '
  //  Call this function when the page has been loaded
  function '.$init_name.'()
  {';
    foreach($init_events as $init_event)
    {
      if ($init_event)
      {
        $return .= '
    '.$init_event;
      }
    }
    $return .= '
  }
';
    $return .= $this->getOnloadJs()."\n";

    $return .= $this->getOpenMarkerInfoWindowJs();
    
    
    return $return;
  }

  public function getJavascriptHtmlSource()
  {
  	$js = $this->getJavascript();
    $return = "
  <script type='text/javascript'>
    //<![CDATA[
      $js
    //]]>
  </script>
  ";
    return $return;
  }
  
  /**
   * returns the URLS for the google map Javascript file
   * @return string $js_url
   */
  public function getGoogleJsUrl($auto_load = true)
  {

    return $this->getClient()->getGoogleJsUrl($auto_load);
  }

  /**
   * Adds an icon to be loaded
   * @param Radcodes_Lib_Google_Map_Icon $icon A google Map Icon
   */
  public function addIcon($icon)
  {
    $this->icons[$icon->getName()] = $icon;
  }

  /**
   * returns the Radcodes_Lib_Google_Map_Icon corresponding to a name
   *
   * @param string $name
   * @return Radcodes_Lib_Google_Map_Icon
   *
   * @author Vincent
   * @since 2008-12-02
   */
  public function getIconByName($name)
  {

    return $this->icons[$name];
  }

  /**
   * @param Radcodes_Lib_Google_Map_Marker $marker a marker to be put on the map
   */
  public function addMarker($marker)
  {
    array_push($this->markers,$marker);
    return $this;
  }
  /**
   * @param Radcodes_Lib_Google_Map_Marker[] $markers marker to be put on the map
   */
  public function setMarkers($markers)
  {
    $this->markers = $markers;
    return $this;
  }
  /**
   * @param Radcodes_Lib_Google_Map_Event $event an event to be attached to the map
   */
  public function addEvent($event)
  {
    array_push($this->events,$event);
  }

  /**
   * checks which markers have special icons and binds these icons to the map
   * 
   * @return void
   */
  public function loadMarkerIcons()
  {
    foreach($this->markers as $marker)
    {
      if ($marker->getIcon() instanceof Radcodes_Lib_Google_Map_Icon)
      {
        $this->addIcon($marker->getIcon());
      }
    }
  }
  /**
   * Returns the javascript string which defines the icons
   * @return string
   */
  public function getIconsJs()
  {
    $this->loadMarkerIcons();
    $return = '';
    foreach ($this->icons as $icon)
    {
      $return .= $icon->getIconJs();
    }

    return $return;
  }
  /**
   * Returns the javascript string which defines the markers
   * @return string
   */
  public function getMarkersJs()
  {
    $return = '';
    foreach ($this->markers as $marker)
    {
      $return .= $marker->toJs($this->getJsName());
      $return .= "\n    ";
    }

    return $return;
  }

  /*
   * Returns the javascript string which defines events linked to the map
   * @return string
   */
  public function getEventsJs()
  {
    $return = '';
    foreach ($this->events as $event)
    {
      $return .= $event->getEventJs($this->getJsName());
      $return .= "\n";
    }
    
    return $return;
  }

  /*
   * Gets the Code to execute after Js initialization
   * @return string $after_init_js
   */
  public function getAfterInitJs()
  {
    return $this->after_init_js;
  }
  /*
   * Sets the Code to execute after Js initialization
   * @param string $after_init_js Code to execute
   */
  public function addAfterInitJs($after_init_js)
  {
    array_push($this->after_init_js,$after_init_js);
  }

  
  public function addGlobalVariable($name, $value='null')
  {
    $this->global_variables[$name] = $value;
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
    return $this;
  }
  
  /**
   * 
   * @return integer $zoom
   */
  public function getZoom()
  {

    return $this->getOption('zoom');
  }
  
  /**
   * 
   * @param integer $zoom
   * @return void
   */
  public function setZoom($zoom)
  {
    $this->setOption('zoom',$zoom);
    return $this;
  }
  
  /**
   * Sets the center of the map at the beginning
   *
   * @param float $lat
   * @param float $lng
   * @since 2009-08-20 fabriceb now everything is in the options array
   */
  public function setCenter($lat=null,$lng=null)
  {
    $this->setOption('center',new Radcodes_Lib_Google_Map_Coord($lat, $lng));
    return $this;
  }
  
  /**
   *
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   * @since 2009-08-20 fabriceb now everything is in the options array
   */
  public function getCenterCoord()
  {

    return $this->getOption('center');
  }
   /**
   *
   * @return float
   * @author fabriceb
   * @since 2009-05-02
   */
  public function getCenterLat()
  {

    return $this->getCenterCoord()->getLatitude();
  }
    /**
   *
   * @return float
   * @author fabriceb
   * @since 2009-05-02
   */
  public function getCenterLng()
  {
    return $this->getCenterCoord()->getLongitude();
  }


  /**
   * gets the width of the map in pixels according to container style
   * @return integer
   * @author fabriceb
   * @since 2009-05-03
   */
  public function getWidth()
  {
    // percentage or 0px
    if (substr($this->getContainerStyle('width'),-2,2) != 'px')
    {
      
      return false;
    }
    
    return intval(substr($this->getContainerStyle('width'),0,-2));
  }

  /**
   * gets the width of the map in pixels according to container style
   * @return integer
   * @author fabriceb
   * @since 2009-05-03
   */
  public function getHeight()
  {
    // percentage or 0px
    if (substr($this->getContainerStyle('height'),-2,2) != 'px')
    {
      
      return false;
    }

    return intval(substr($this->getContainerStyle('height'),0,-2));
  }

  /**
   * sets the width of the map in pixels
   *
   * @param integer
   * @author fabriceb
   * @since 2009-05-03
   */
  public function setWidth($width)
  {
    if (is_int($width))
    {
      $width = $width.'px';
    }
    $this->setContainerStyle('width', $width);
    return $this;
  }

  /**
   * sets the width of the map in pixels
   *
   * @param integer
   * @author fabriceb
   * @since 2009-05-03
   */
  public function setHeight($height)
  {
    if (is_int($height))
    {
      $height = $height.'px';
    }
    $this->setContainerStyle('height',$height);
    return $this;
  }


  /**
   * Returns the URL of a static version of the map (when JavaScript is not active)
   * Supports only markers and basic parameters: center, zoom, size.
   * @param string $map_type = 'mobile'
   * @param string $hl Language (fr, en...)
   * @return string URL of the image
   * @author Laurent Bachelier
   */
  public function getStaticMapUrl($maptype='mobile', $hl='fr')
  {
    $params = array(
      'maptype' => $maptype,
      'zoom'    => $this->getZoom(),
      'center'  => $this->getCenterLat().','.$this->getCenterLng(),
      'size'    => $this->getWidth().'x'.$this->getHeight(),
      'hl'      => $hl,
      'markers' => $this->getMarkersStatic()
    );
    $pairs = array();
    foreach($params as $key => $value)
    {
      $pairs[] = $key.'='.$value;
    }

    return 'http://maps.google.com/staticmap?'.implode('&',$pairs);
  }

  /**
   * Returns the static code to create markers
   * @return string
   * @author Laurent Bachelier
   */
  protected function getMarkersStatic()
  {
    $markers_code = array();
    foreach ($this->markers as $marker)
    {
      $markers_code[] = $marker->getMarkerStatic();
    }

    return implode('|',$markers_code);
  }

  /**
   *
   * calculates the center of the markers linked to the map
   *
   * @return Radcodes_Lib_Google_Map_Coord
   * @author fabriceb
   * @since 2009-05-02
   */
  public function getMarkersCenterCoord()
  {

    return Radcodes_Lib_Google_Map_Marker::getCenterCoord($this->markers);
  }

  /**
   * sets the center of the map at the center of the markers
   *
   * @author fabriceb
   * @since 2009-05-02
   */
  public function centerOnMarkers()
  {
    $center = $this->getMarkersCenterCoord();

    $this->setCenter($center->getLatitude(), $center->getLongitude());
    return $this;
  }

  /**
   *
   * calculates the zoom which fits the markers on the map
   *
   * @param integer $margin a scaling factor around the smallest bound
   * @return integer $zoom
   * @author fabriceb
   * @since 2009-05-02
   */
  public function getMarkersFittingZoom($margin = 0, $default_zoom = 14)
  {
    $bounds = Radcodes_Lib_Google_Map_Bounds::getBoundsContainingMarkers($this->markers, $margin);
    //print_r($bounds);
    
    if (!$this->getWidth())
    {
      $min = $this->getHeight();
    }
    else if (!$this->getHeight()) 
    {
      $min = $this->getWidth();
    }
    else 
    {
      $min = min($this->getWidth(),$this->getHeight());
    }

    return $bounds->getZoom($min, $default_zoom);
  }

  /**
   * sets the zoom of the map to fit the markers (uses mercator projection to guess the size in pixels of the bounds)
   * WARNING : this depends on the width in pixels of the resulting map
   *
   * @param integer $margin a scaling factor around the smallest bound
   * @author fabriceb
   * @since 2009-05-02
   */
  public function zoomOnMarkers($margin = 0, $default_zoom = 14)
  {
    $this->setZoom($this->getMarkersFittingZoom($margin, $default_zoom));
    return $this;
  }

   /**
   * sets the zoom and center of the map to fit the markers (uses mercator projection to guess the size in pixels of the bounds)
   *
   * @param integer $margin a scaling factor around the smallest bound
   * @author fabriceb
   * @since 2009-05-02
   */
  public function centerAndZoomOnMarkers($margin = 0, $default_zoom = 14)
  {
    $this->centerOnMarkers();
    $this->zoomOnMarkers($margin, $default_zoom);
    return $this;
  }

  /**
   *
   * @return Radcodes_Lib_Google_Map_Bounds
   * @author fabriceb
   * @since Jun 2, 2009 fabriceb
   */
  public function getBoundsFromCenterAndZoom()
  {

    return Radcodes_Lib_Google_Map_Bounds::getBoundsFromCenterAndZoom($this->getCenterCoord(),$this->getZoom(),$this->getWidth(),$this->getHeight());
  }


  
  /**
   * $directions getter
   *
   * @return array $directions
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-13 17:18:29
   */
  public function getDirections()
  {
    
    return $this->directions;
  }
  
  /**
   * $directions setter
   *
   * @param array $directions
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-13 17:21:18
   */
  public function setDirections($directions = null)
  {
    $this->directions = $directions;
    return $this;
  }
  
  /**
   * Add direction to list ($this->directions)
   *
   * @param Radcodes_Lib_Google_Map_Direction $directions
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 14:59:55
   */
  public function addDirection($direction = null)
  {
    if (!$direction instanceof Radcodes_Lib_Google_Map_Direction)
    {
      throw new Radcodes_Lib_Google_Map_Exception('The direction must be an instance of Radcodes_Lib_Google_Map_Direction !');
    }
    
    array_push($this->directions, $direction);
  }
  
  /**
   * Get the directions javascript code
   *
   * @return string $js_code
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 15:03:00
   */
  public function getDirectionsJs()
  {
    $js_code = '';
    
    foreach ($this->directions as $direction)
    {
      $js_code .= $direction->toJs($this->getJsName());
      $js_code .= "\n      ";
    }

    return $js_code;
  }
  
  public function getOpenMarkerInfoWindowEventJs($marker, $infowindow)
  {
  	$js_code = $infowindow->getName() . '.open('.$this->getJsName().','.$marker->getName().')';  	
  	
  	$map_name = $this->getJsName();
  	$marker_name = $marker->getName();
  	$infowindow_name = $infowindow->getName();
  	
  	$js_code = "pop_infowindow_$map_name($marker_name,$infowindow_name);";
  	return $js_code;
  }
  
  
  public function getOpenMarkerInfoWindowJs()
  {
  	$map_name = $this->getJsName();
  	$curr_infowindow = "curr_infowindow_$map_name";
  	
  	$container_id = $this->getContainerId();
  	
    $js_code = "
    var $curr_infowindow;
    function pop_infowindow_$map_name(marker, infowindow)
    {
      var myFx = new Fx.Scroll(window).toElement('$container_id');
    
      if($curr_infowindow) { $curr_infowindow.close(); } 
      infowindow.open($map_name,marker);
      $curr_infowindow = infowindow; 
      
    }
    ";
    
    return $js_code;
  }
  
}
