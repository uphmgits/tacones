<?php

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * @author Vincent Guillon <vincentg@theodo.fr>
 * @since 2009-11-20 16:14:23
 */
class Radcodes_Lib_Google_Map_DirectionWaypoint
{  
  protected $location;
  protected $stopover;
  
  /**
   * Construct Radcodes_Lib_Google_Map_Direction object
   *
   * @param Radcodes_Lib_Google_Map_Coord $origin The coordinates of origin
   * @param Radcodes_Lib_Google_Map_Coord $destination The coordinates of destination
   * @param string $js_name The js var name
   * @param array $options Array of options
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-10-30 17:20:47
   */
  public function __construct($location = null, $stopover = true)
  {
    $this->setLocation($location);
    $this->setStopOver($stopover);
  }
  
  /**
   * $location getter
   *
   * @return Radcodes_Lib_Google_Map_Coord $this->location
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 16:16:55
   */
  public function getLocation()
  {
    
    return $this->location;
  }
  
  /**
   * $stopover getter
   *
   * @return boolen $this->stopover
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 16:17:14
   */
  public function getStopOver()
  {
    
    return $this->stopover;
  }
  
  /**
   * $location setter
   *
   * @param Radcodes_Lib_Google_Map_Coord $location
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 16:17:42
   */
  public function setLocation($location = null)
  {
    if (!$location instanceof Radcodes_Lib_Google_Map_Coord)
    {
      throw new Radcodes_Lib_Google_Map_Exception('The destination must be an instance of Radcodes_Lib_Google_Map_Coord !');
    }
    
    $this->location = $location;
  }
  
  /**
   * $stopover setter
   *
   * @param boolean $stopover
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 16:19:37
   */
  public function setStopOver($stopover = true)
  {
    $this->stopover = $stopover;
  }
  
  /**
   * Generate javascript code fo Radcodes_Lib_Google_Map_Direction waypoints option
   *
   * @return string
   * @author Vincent Guillon <vincentg@theodo.fr>
   * @since 2009-11-20 16:31:42
   */
  public function optionsToJs()
  {
    $stopover = $this->getStopOver() ? 'true' : 'false';
    
    return '{location : '.$this->getLocation()->toJs().', stopover: '.$stopover.'}';
  }
}