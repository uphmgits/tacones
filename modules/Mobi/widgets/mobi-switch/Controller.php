<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8822 2011-04-09 00:30:46Z john $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Widget_MobiSwitchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $session = new Zend_Session_Namespace('mobile');
    $this->view->mobile = $session->mobile;
  }

}