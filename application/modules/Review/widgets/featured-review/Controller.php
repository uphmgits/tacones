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
 
class Review_Widget_FeaturedReviewController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = array(
      'featured' => 1,
      'order' => 'random'
    );
    
    $this->view->review = Engine_Api::_()->getDbtable('reviews', 'review')->getReview($params);
    
    if (!$this->view->review) {
      return $this->setNoRender();
    }
    
  }

}