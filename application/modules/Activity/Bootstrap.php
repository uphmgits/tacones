<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Bootstrap.php 9325 2011-09-27 00:11:15Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Activity_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') 
        . 'application/modules/Activity/externals/scripts/core.js');
  }
}
