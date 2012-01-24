<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Bootstrap.php 8822 2011-04-09 00:30:46Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function _bootstrap()
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Mobi_Plugin_Core);
  }
}