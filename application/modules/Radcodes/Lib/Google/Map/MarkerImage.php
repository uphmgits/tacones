<?php

/*
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * A GoogleMap MarkerImage
 * @author Maxime Picaud
 * 
 */
class Radcodes_Lib_Google_Map_MarkerImage
{
  //String $url url of image
  protected $url;
   
  //Size $size array('width' => null , 'height' => null ) 
  protected $size = array(
    'width' => null,
    'height' => null
  );
   
  //Point $origin array('x' => null , 'y' => null ) 
  protected $origin = array(
    'x' => null,
    'y' => null
  );
  
  //Point $anchor array('x' => null , 'y' => null ) 
  protected $anchor = array(
    'x' => null,
    'y' => null
  );
  
 
  
  /**
   * @param string $js_name Javascript name of the marker
   * @param string $url url of image 
   * @param array $size array('width' => $width,'height' => $height)
   * @param array $origin array('x' => $x,'y' => $y)
   * @param array $anchor array('x' => $x,'y' => $y)
   * @return unknown_type
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function __construct($url,$size=null,$origin=null,$anchor=null)
  {
    $this->url = $url;
    if(is_array($size) && isset($size['width']) && isset($size['height']))
    {
      $this->setSize($size['width'],$size['height']);
    }
    
    if(is_array($origin) && isset($origin['x']) && isset($origin['y']))
    {
      $this->setOrigin($origin['x'],$origin['y']);
    }
    
    if(is_array($anchor) && isset($anchor['x']) && isset($anchor['y']))
    {
      $this->setAnchor($anchor['x'],$anchor['y']);
    }
  }
  
  /**
   * 
   * @return string $url
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getUrl()
  {
    return $this->url;
  }
  
  /**
   * 
   * @return array $size
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getSize()
  {
    return $this->size;
  }
  
  /**
   * 
   * @return int $size['width']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getWidth()
  {
    return $this->size['width'];
  }
  
  /**
   * 
   * @return int $size['height']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getHeight()
  {
    return $this->size['height'];
  }
  
  
  /**
   * 
   * @param int $width
   * @param int $height
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function setSize($width,$height)
  {
    $this->size['width'] = $width;
    $this->size['height'] = $height;
  }
  
  /**
   * 
   * @return array $origin = array('width' => $width, 'height' => $height)
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  
  /**
   * @return int $origin['x']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getOriginX()
  {
    return $this->origin['x'];
  }
  
  /**
   * 
   * @return int $origin['y']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getOriginY()
  {
    return $this->origin['y'];
  }
  
  /**
   * 
   * @param int $x
   * @param int $y
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function setOrigin($x,$y)
  {
    $this->origin['x'] = $x;
    $this->origin['y'] = $y;
  }
  
  /**
   * 
   * @return array $anchor = array('x' => $x, 'y' => $y)
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getAnchor()
  {
    return $this->anchor;
  }
  
  /**
   * 
   * @return int $anchor['x']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getAnchorX()
  {
    return $this->anchor['x'];
  }
  
  /**
   * 
   * @return int $anchor['y']
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function getAnchorY()
  {
    return $this->anchor['y'];
  }
  
  /**
   * 
   * @param int $x
   * @param int $y
   * @return unknown_type
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function setAnchor($x,$y)
  {
    $this->anchor['x'] = $x;
    $this->anchor['y'] = $y;
  }

  /**
   * 
   * @return string js for Size
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function sizeToJs()
  {
    $size = 'null';
    if(!empty($this->size['width']) && !empty($this->size['height']))
    {
      $size = 'new google.maps.Size('.$this->getWidth().','.$this->getHeight().')'; 
    }
    
    return $size;
  }
 
  /**
   * 
   * @return string js for Origin Point
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function originToJs()
  {
    $origin = 'null';
    if(!empty($this->origin['x']) && !empty($this->origin['y']))
    {
      $origin = 'new google.maps.Point('.$this->getOriginX().','.$this->getOriginY().')'; 
    }
    
    return $origin;
  }
  
  /**
   * 
   * @return string js for Anchor Point
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function anchorToJs()
  {
    $anchor = 'null';
    if(!empty($this->anchor['x']) && !empty($this->anchor['y']))
    {
      $anchor = $this->getName().'new google.maps.Point('.$this->getAnchorX().','.$this->getAnchorY().')'; 
    }
    
    return $anchor;
  }

  /**
   * 
   * @return string js code to create the markerImage
   * @author Maxime Picaud
   * @since 4 sept. 2009
   */
  public function toJs()
  {
    $params = array();
    
    $params[] = '"'.$this->getUrl().'"';
    $params[] = $this->sizeToJs();
    $params[] = $this->originToJs();
    $params[] = $this->anchorToJs();
    
    $return = 'new google.maps.MarkerImage('.implode(',',$params).")";
    
    return $return;
  }

}
