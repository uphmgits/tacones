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

class Review_Plugin_Menus
{

  public function canCreateReviews()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create reviews
    if( !Engine_Api::_()->authorization()->isAllowed('review', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewReviews()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view reviews
    if( !Engine_Api::_()->authorization()->isAllowed('review', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  public function onMenuInitialize_ReviewGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof Review_Model_Review ) {
      $user_id = $subject->user_id;
    } else {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['id'] = $user_id;
    return $params;
  }

  public function onMenuInitialize_ReviewGutterCreate($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject('review');

    if( $review->isOwner($viewer) || $review->isUser($viewer)) {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('review', $viewer, 'create') ) {
      return false;
    }

    if (Engine_Api::_()->review()->hasReviewed($viewer, $review->getUser())) {
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['to'] = $review->user_id;
    return $params;
  }

  public function onMenuInitialize_ReviewGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject('review');

    if( !$review->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['review_id'] = $review->getIdentity();
    return $params;
  }

  public function onMenuInitialize_ReviewGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $review = Engine_Api::_()->core()->getSubject('review');

    if( !$review->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['review_id'] = $review->getIdentity();
    return $params;
  }
  
  
  public function onMenuInitialize_UserProfileReview($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if(!($subject instanceof User_Model_User) ) {
      return false;
    }
    
    if( !Engine_Api::_()->authorization()->isAllowed('review', $subject, 'reviewed') ) {
      return false;
    }    
    
    $params = $row->params;
    
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ($subject->isSelf($viewer)) {
      return false;
    }
    
    $review = Engine_Api::_()->review()->getOwnerReviewForUser($viewer, $subject);
    
    if ($review) {

        return array(
          'label' => 'View Your Review',
          'icon' => 'application/modules/Review/externals/images/review.png',
          'route' => 'review_profile',
          'params' => array(
            'module' => 'review',
            'controller' => 'index',
            'action' => 'view',
            'review_id' => $review->getIdentity(),
            'slug' => $review->getSlug(),
          ),
        );
      /*
      if ($review->authorization()->isAllowed($viewer, 'edit')) 
      {
        return array(
          'label' => 'Update Your Review',
          'icon' => 'application/modules/Review/externals/images/review_edit.png',
          'route' => 'review_specific',
          'params' => array(
            'module' => 'review',
            'controller' => 'index',
            'action' => 'edit',
            'review_id' => $review->getIdentity(),
          ),
        );
      }
      else {
        return array(
          'label' => 'View Your Review',
          'icon' => 'application/modules/Review/externals/images/review.png',
          'route' => 'review_profile',
          'params' => array(
            'module' => 'review',
            'controller' => 'index',
            'action' => 'view',
            'review_id' => $review->getIdentity(),
            'slug' => $review->getSlug(),
          ),
        );
      }
      */
    }
    else {
      $params['params']['to'] = $subject->getIdentity();
    }

    $params['icon'] = 'application/modules/Review/externals/images/create.png';

    return $params;
  }
  
  
}