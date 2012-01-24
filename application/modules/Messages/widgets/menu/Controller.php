<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9308 2011-09-22 00:36:41Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Messages_Widget_MenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('messages_main');
  }
}
