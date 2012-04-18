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
class Review_Widget_ProfileRatingController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }   

    if( !($subject instanceof User_Model_User) ) {
      return $this->setNoRender();
    }    
    
    if( !Engine_Api::_()->authorization()->isAllowed('review', $subject, 'reviewed') ) {
      return $this->setNoRender();
    }    
    
    if ($viewer->getIdentity()) {
      $this->view->user_review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $subject);
      $this->view->can_review = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'review', 'create');
    }
    
    $this->view->total_review = Engine_Api::_()->review()->getUserReviewCount($subject);
    $this->view->average_rating = Engine_Api::_()->review()->getUserAverageRating($subject);
    
    $this->view->total_recommend = Engine_Api::_()->review()->getUserRecommendCount($subject);
    
  }

}