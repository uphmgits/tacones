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
 
 
 
class Review_Widget_ListMenuController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	/*
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation = $navigation = new Zend_Navigation();
    
    $navigation->addPage(array(
      'label' => Zend_Registry::get('Zend_Translate')->_('Browse Reviews'),
      'route' => 'review_browse',
      'module' => 'review',
      'controller' => 'index',
      'action' => 'browse',
    ));

    if( $viewer->getIdentity() )
    {
	    $navigation->addPage(array(
	      'label' => Zend_Registry::get('Zend_Translate')->_('My Reviews'),
	      'route' => 'review_manage',
	      'module' => 'review',
	      'controller' => 'index',
	      'action' => 'manage',
	    ));
	    
	    if (Engine_Api::_()->authorization()->isAllowed(review, $viewer, 'create'))
	    {
	      $navigation->addPage(array(
	        'label' => Zend_Registry::get('Zend_Translate')->_('Post New Review'),
	        'route' => 'review_create',
	        'module' => 'review',
	        'controller' => 'index',
	        'action' => 'create'
	      ));
	    }
    }
    */
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('review_main');
    
  }

}