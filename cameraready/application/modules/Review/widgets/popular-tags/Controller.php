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
 
 
 
class Review_Widget_PopularTagsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $max = $this->_getParam('max', 50);
    $order = $this->_getParam('order', 'text');
    
    $this->view->tags = $tags = Engine_Api::_()->review()->getPopularTags(array('limit' => $max, 'order' => $order));
    
    if (empty($tags))
    {
      return $this->setNoRender();
    } 
    
  }

}