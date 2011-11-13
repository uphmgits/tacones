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
 
class Radcodes_Widget_AdminModulesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$modules = Engine_Api::_()->radcodes()->getRest('store')->getModules();
  	$this->view->modules = $modules;
  	return;
  }
}