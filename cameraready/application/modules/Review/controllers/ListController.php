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
 
 
class Review_ListController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('review', null, 'view')->isValid() ) return;
    
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');

    // Get quick navigation
    $this->view->listNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_toplist');     
    
  }
  
  
  public function topRatedMembersAction()
  {      
    $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.toplimit', 10);  
      
    $params = array(
      'limit' => $limit
    );
    
    $this->view->stats = Engine_Api::_()->getDbtable('reviews', 'review')->getAverageRatingUsers($params);
      
  }
  
  
  public function mostRecommendedMembersAction()
  {      
    $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.toplimit', 10);  
      
    $params = array(
      'limit' => $limit,
      'recommend' => 1,
    );
    
    $this->view->stats = Engine_Api::_()->getDbtable('reviews', 'review')->getUsersReviewCount($params);
      
  }

  public function mostReviewedMembersAction()
  {      
    $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.toplimit', 10);  
      
    $params = array(
      'limit' => $limit,
    );
    
    $this->view->stats = Engine_Api::_()->getDbtable('reviews', 'review')->getUsersReviewCount($params);
      
  }
  
  
  public function topReviewersAction()
  {
    $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('review.toplimit', 10); 
    
    $params = array(
      'limit' => $limit
    );
    
    $this->view->stats = Engine_Api::_()->getDbtable('reviews', 'review')->getOwnersReviewCount($params);
  }
  
}

