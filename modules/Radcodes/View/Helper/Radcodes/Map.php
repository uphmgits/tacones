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

class Radcodes_View_Helper_Radcodes_Map extends Radcodes_View_Helper_Abstract
{

  public function map()
  {
    return $this;
  }
  
  public function items($items, $options = array())
  {
    $options['markers'] = array();
    foreach ($items as $item)
    {
      $marker = $this->itemMarker($item, $options);
      if ($marker instanceof Radcodes_Lib_Google_Map_Marker)
      {
        array_push($options['markers'], $marker);
      }
    }
    
    if (!isset($options['google_map']))
    {
      $google_map = 'radcodes_map';
    }
    else {
      $google_map = $options['google_map'];
    }
    
    $map_js = $this->render($google_map, $options);
    
    return $map_js;
  }
  
  
  public function item(Core_Model_Item_Abstract $item, $options = array())
  {
    $marker = $this->itemMarker($item, $options);
    if (!$marker) return;
    
    if (isset($options['init_open_infowindow']))
    {
      $options['init_open_infowindow'] = $marker->getName();;
    }
    
    if (!isset($options['google_map']))
    {
      $google_map = 'radcodes_map_'.$item->getGuid();
    }
    else {
      $google_map = $options['google_map'];
    }
    $options['markers'] = array($marker);

    $map_js = $this->render($google_map, $options);
    
    if (null !== ($dragend_event = $marker->getEvent('dragend')))
    {
      $map_js .= $this->view->partial('_map_jsDraggable.tpl', 'radcodes', array(
        'marker'=>$marker, 
        'function'=>$dragend_event->getFunction(),
        'lat_id' => $options['draggable']['lat_id'] ? $options['draggable']['lat_id'] : 'lat',
        'lng_id' => $options['draggable']['lng_id'] ? $options['draggable']['lng_id'] : 'lng',
      ));
    }
    
    return $map_js;
  }
  
  
  /*
   * @param Radcodes_Lib_Google_Map $google_map
   */  
  public function render($google_map, $options = array())
  {
  	if (!($google_map instanceof Radcodes_Lib_Google_Map))
  	{
  		$google_map = Engine_Api::_()->getApi('map', 'radcodes')->factory($google_map);
  	}
  	
  	if (isset($options['markers']))
  	{
  	  foreach ($options['markers'] as $marker)
  	  {
  	    $google_map->addMarker($marker);
  	    
  	    if (isset($options['init_open_infowindow']))
  	    {
  	      if ($options['init_open_infowindow'] == $marker->getName())
  	      {
				    $after_init_js = $google_map->getOpenMarkerInfoWindowEventJs($marker, $marker->getHtmlInfoWindow());
				    $google_map->addAfterInitJs($after_init_js);
  	      }
  	    }
  	    
  	  }
  	}
  	
    if (isset($options['width']))
    {
      $google_map->setWidth($options['width']);
    }
    if (isset($options['height']))
    {
      $google_map->setHeight($options['height']);
    }
    if (isset($options['center']))
    {
      $google_map->setCenter($options['center']['lat'], $options['center']['lng']);
    }
    else
    {
      $google_map->centerOnMarkers();
    }
    
    if (isset($options['zoom']))
    {
      $google_map->setZoom($options['zoom']);
    }
    else
    {
      $zoom_margin = isset($options['zoom_margin']) ? $options['zoom_margin'] : 0.2;
      $google_map->zoomOnMarkers($zoom_margin, 14);
    }
    
    $this->view->headScript()->appendFile($google_map->getGoogleJsUrl());
    
    $return = $google_map->getContainer()
            . $google_map->getJavascriptHtmlSource();

    return $return;
  }
  
  /**
   * 
   * @param Core_Model_Item_Abstract $item
   * @param array $options
   * @return Radcodes_Lib_Google_Map_Marker
   */
  public function itemMarker($item, $options = array())
  {
    if ($item instanceof Radcodes_Lib_Google_Map_Marker)
    {
      return $item;
    }
    
    $location = $item->getLocation();
    
    if (!$location) return;
    
    $infowindow_js_name = 'radcodes_map_infowindow_'.$item->getGuid();
    $infowindow_content = $this->view->partial('_map_infoWindow.tpl', 'radcodes', array('item'=>$item, 'location'=>$location));
    $infowindow = new Radcodes_Lib_Google_Map_InfoWindow($infowindow_content, array(), $infowindow_js_name);    
    
    $marker_js_name = 'radcodes_map_marker_'.$item->getGuid();
    $marker_options = array('title' => $item->getTitle());
    if (isset($options['draggable']))
    {
      $marker_options['draggable'] = 'true';
      $marker_options['title'] = $this->view->translate('Drag me adjust coordinate');
      $dragend_event = new Radcodes_Lib_Google_Map_Event('dragend','marker_locator_coordinate', false);
    }       
    
    $marker = new Radcodes_Lib_Google_Map_Marker($location->lat, $location->lng, $marker_options, $marker_js_name);
    $marker->addHtmlInfoWindow($infowindow);

    if (isset($dragend_event))
    {
      $marker->addEvent($dragend_event, 'dragend');
    }
    
    if (isset($options['marker_icon']))
    {
    	$marker_icon = $options['marker_icon'];
    }
    else {
    	$marker_icon = Engine_Api::_()->getApi('settings', 'core')->getSetting($item->getType().'.mapmarkericonurl');
    }
    if ($marker_icon)
    {
    	if (!($marker_icon instanceof Radcodes_Lib_Google_Map_MarkerImage))
    	{
    		$marker_icon = new Radcodes_Lib_Google_Map_MarkerImage($marker_icon);
    	}
    	$marker->setOption('icon', $marker_icon);
    }

    return $marker;
  }
  
}
