<?php

/**
 * 
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * 
 * A googleMap Event
 * @author Fabrice Bernhard
 * 
 */
class Radcodes_Lib_Google_Map_Event
{
  protected $trigger;
  protected $function;
  protected $encapsulate_function;
  
  /**
   * @param string $trigger action that will trigger the event
   * @param string $function the javascript function to be executed
   * @param string $encapsulate_function
   * @author Fabrice Bernhard
   */
  public function __construct($trigger,$function,$encapsulate_function=true)
  {
    $this->trigger      = $trigger;
    $this->function     = $function;
    $this->encapsulate_function = $encapsulate_function;
  }
  
  /**
   * @return string $trigger  action that will trigger the event   
   */
  public function getTrigger()
  {
    
    return $this->trigger;
  }
  /**   
   * @return string $function the javascript function to be executed
   */
  public function getFunction()
  {
    if (!$this->encapsulate_function)
    {
      
      return $this->function;
    }
    else
    {
      
      return 'function() {
      '.$this->function.'
    }';
    }
  }
  
  /**
   * returns the javascript code for attaching a Google event to a javascript_object
   *
   * @param string $js_object_name
   * @return string
   * @author Fabrice Bernhard
   */
  public function getEventJs($js_object_name)
  {
    
    return 'google.maps.event.addListener('.$js_object_name.', "'.$this->getTrigger().'", '.$this->getFunction().');';
  }
  
   /**
   * returns the javascript code for attaching a dom event to a javascript_object
   *
   * @param string $js_object_name
   * @return string
   * @author Fabrice Bernhard
   */
  public function getDomEventJs($js_object_name)
  {
    
    return 'google.maps.event.addDomListener('.$js_object_name.', "'.$this->getTrigger().'", '.$this->getFunction().');';
  }
  
}
