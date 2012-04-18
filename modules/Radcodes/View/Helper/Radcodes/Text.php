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

class Radcodes_View_Helper_Radcodes_Text extends Radcodes_View_Helper_Abstract
{

  public function text()
  {
    return $this;
  }
  
  public function string()
  {
    return $this;
  }
  
  public function truncate($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
  {
  	return Radcodes_Lib_Helper_Text::truncate($text, $length, $truncate_string, $truncate_lastspace);
  }
  
  
  public function slugify($text, $options = array())
  {
    return Radcodes_Lib_Helper_Text::slugify($text, $options);
  }
  
  public function viewmore($text, $length = 255, $max = 1027)
  {
    $viewmore = new Engine_View_Helper_ViewMore();
    $viewmore->setView($this->view);
    $viewmore->setMoreLength($length);
    $viewmore->setMaxLength($max);
    return $viewmore->viewMore($text);
  }
}
