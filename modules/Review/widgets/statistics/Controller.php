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
 
 
 
class Review_Widget_StatisticsController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
  	$this->view->distributions = Engine_Api::_()->getDbtable('reviews', 'review')->getDistributions();
  }

}