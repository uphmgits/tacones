<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <john@socialengine.com>
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    
    // Get form
    $this->view->form = $form = new Poll_Form_Search();

    // Process form
    $values = array('browse' => 1);
    if( $form->isValid($p) ) {
      $values = $form->getValues();
    }
    $this->view->formValues = array_filter($values);

    if( @$values['show'] == 2 && $viewer->getIdentity() ) {
      // Get an array of friend ids
      $values['users'] = $viewer->membership()->getMembershipsOfIds();
    }
    unset($values['show']);
  }
}
