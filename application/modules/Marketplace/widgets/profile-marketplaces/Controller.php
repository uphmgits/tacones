<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: Controller.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */
class Marketplace_Widget_ProfileMarketplacesController extends Engine_Content_Widget_Abstract
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
	
	$this->view->cats = $cats = Engine_Api::_()->marketplace()->getUserCategories($subject->getIdentity())->toArray();
	if(!$cats){
      return $this->setNoRender();
	}
	
    $this->view->paginator = $paginator = Engine_Api::_()->marketplace()->getMarketplacesPaginator(array(
      'orderby' => 'creation_date',
      'page' => '1',
      'limit' => '10',
      'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
    ));
    
    $this->view->paginator->setCurrentPageNumber(1);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      //$this->_childCount = $paginator->getTotalItemCount();
    }
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->items_per_page = $settings->getSetting('marketplace_page', 10);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}