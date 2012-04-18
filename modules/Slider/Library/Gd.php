<?php

class Slider_Library_Gd extends Engine_Image_Adapter_Gd
{

  protected $_quality = 100;

  public function set_quality($quality) {
      if ($quality >= 0 and $quality <= 100)
          $this->_quality = (int)$quality;
  }
}
