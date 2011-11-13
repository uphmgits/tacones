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
 
 
 
class Review_Widget_CreateNewController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('review', $viewer, 'create');
    
    if (!$this->view->can_create) {
      return $this->setNoRender();
    }
    
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_quick'); 
    
  }

}