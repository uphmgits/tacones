<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
  }  
  
  /**
   * Adds the view helper path for this module to the view
   * 
   * @return Engine_Application_Bootstrap_Abstract
   */
  public function initViewHelperPath()
  {
    parent::initViewHelperPath();
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $view = $viewRenderer->view;
    if( is_null($view) )
    {
      return $this;
    }

    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Radcodes/View/Helper', 'Radcodes_View_Helper');

    return $this;
  }
  
}