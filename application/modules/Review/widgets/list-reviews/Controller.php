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
 
 
 
class Review_Widget_ListReviewsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
  
    $limit = $this->_getParam('max', 5);
    if (!$limit) {
      return $this->setNoRender();
    }
    
    $limit = $this->_getParam('max', 5);
    
    
    $params = array(
      'order' => 'recent',
      'limit' => $limit,
    );
    if ($owner = $this->_getParam('owner')) {
      $params['owner'] = $owner;
    }
    if ($user = $this->_getParam('user')) {
      $params['user'] = $user;
    }
    if (($recommend = $this->_getParam('recommend')) > 0 ) {
      $params['recommend'] = 1;
    }
    if (($rating = $this->_getParam('rating')) > 0) {
      $params['rating'] = $rating;
    }
    $params['order'] = $this->_getParam('order','recent');
    
    $this->view->paginator = $paginator = Engine_Api::_()->review()->getReviewsPaginator($params);

    $this->view->display_style = $this->_getParam('display_style', 'wide');
    $this->view->showphoto = $this->_getParam('showphoto', $this->view->display_style == 'narrow' ? 0 : 1);
    
    $this->view->showdetails = $this->_getParam('showdetails', $this->view->display_style == 'narrow' ? 0 : 1); 
  }

}