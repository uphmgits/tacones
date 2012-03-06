<?php

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
class Radcodes_Lib_Google_Map_Icon
{
  protected $name         = null;
  protected $icon_src     = '';
  protected $shadow_src   = '';
  protected $options      = array();
  
  public function __construct($name,$icon_src,$options = array(),$shadow_src='')
  {
    $this->name       = $name;
    $this->icon_src   = $icon_src;
    $this->shadow_src = $shadow_src;
    $default_options  = array(
      'width'=>12,
      'height'=>20,
      'shadow_width'=>22,
      'shadow_height'=>20,
      'anchor_x'=>6,
      'anchor_y'=>20,
      'info_window_anchor_x'=>6,
      'info_window_anchor_y'=>3,
    );
    $this->options = array_merge($default_options,$options);
  }
  
  /**
   * Set Icon's path
   * @param string $icon_src Icon's path
   */
  public function setIconSrc($icon_src)
  {
    $this->icon_src=$icon_src;
  }
  /**
   * Get Icon's path
   * @return string   
   */
  public function getIconSrc()
  {
    
    return $this->icon_src;
  }
  /**
   * Set Shadow's path
   * @param string $shadow_src Shadow's path
   */
  public function setShadowSrc($shadow_src)
  {
    $this->shadow_src=$shadow_src;
  }
  /**
   * Get Shadow's path   
   */
  public function getShadowSrc()
  {
    
    return $this->shadow_src;
  }
  /**
   * Get Icon's Javascript variable's name
   * @return string $name 
   */
  public function getName()
  {
    
    return $this->name;
  }
  /**
   * Change Icon's JavaScript name
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * Gets an option
   */
  public function getOption($name)
  {
    
    return $this->options[$name];
  }
  
  /**
   * Returns the javascript code tthat defines an icon
   *
   * @return string
   */
  public function getIconJs()
  {
    $return ="";
    $return .= $this->getName().' = new google.maps.Icon(); ';
    $return .= $this->getName().'.image = "'.$this->getIconSrc().'";';
    $return .= $this->getName().'.iconSize = new google.maps.Size('.$this->getOption('width').','.$this->getOption('height').');';
    $return .= $this->getName().'.iconAnchor = new google.maps.Point('.$this->getOption('anchor_x').','.$this->getOption('anchor_y').');';
    $return .= $this->getName().'.infoWindowAnchor = new google.maps.Point('.$this->getOption('info_window_anchor_x').','.$this->getOption('info_window_anchor_y').');';
    if (!is_null($this->getShadowSrc()))
    {
      $return .= $this->getName().'.shadow = "'.$this->getShadowSrc().'";';        
      $return .= $this->getName().'.shadowSize = new google.maps.Size('.$this->getOption('shadow_width').','.$this->getOption('shadow_height').');';
    }
    
    return $return;
  }
}
