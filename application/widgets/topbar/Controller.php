<?php
class Widget_topbarController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	  
//Check Viewer	  
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

//For Mini Navigation Menu
    $this->view->navigation_mini = $navigation_mini = Engine_Api::_()->getApi('menus', 'core')->getNavigation('core_mini');
	

//For Main Navigation Menu
    $this->view->navigation_main = $navigation_main = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    if(!$require_check && !$viewer->getIdentity()){
      $navigation_main->removePage($navigation_main->findOneBy('route','user_general'));
    }	

    if( $viewer->getIdentity() ) {
      $cartTable = Engine_Api::_()->getDbtable('cart', 'marketplace');
      $this->view->cartitems = $cartTable->select()
                                ->from($cartTable->info('name'), "count(*) as cnt")
                                ->where('user_id = ?', $viewer->getIdentity())
                                ->query()
                                ->fetch()
      ;
    }
    

//Udates Menu
    if( $viewer->getIdentity() )
    {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->notificationOnly = $request->getParam('notificationOnly', false);
    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');	
  

  }
}
