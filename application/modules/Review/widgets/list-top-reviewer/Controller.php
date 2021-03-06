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
 
class Review_Widget_ListTopReviewerController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    $limit = $this->_getParam('max', 5);
    if (!$limit) {
      return $this->setNoRender();
    }
    
    $params = array(
      'limit' => $limit
    );
    
    $this->view->stats = Engine_Api::_()->getDbtable('reviews', 'review')->getOwnersReviewCount($params);

    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1);    
  }

}