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
class Review_Widget_ProfileReviewsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
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
    
    $this->view->items_per_page = $max = $this->_getParam('max', 5);
    $this->view->showdetails = $this->_getParam('showdetails', 0);

    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator(array(
      'order' => 'recent',
      'user' =>  $subject,
      'limit' => $this->view->items_per_page,
    ));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    if ($viewer->getIdentity()) {
      $this->view->user_review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $subject);
      $this->view->can_review = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'review', 'create');
    }
    
    $this->view->total_review = Engine_Api::_()->review()->getUserReviewCount($subject);
    $this->view->average_rating = Engine_Api::_()->review()->getUserAverageRating($subject);
    $this->view->distributions = Engine_Api::_()->review()->getUserReviewDistributions($subject);   
    
    $this->view->total_recommend = Engine_Api::_()->review()->getUserRecommendCount($subject);
    
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}