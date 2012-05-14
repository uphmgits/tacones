<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 8968 2011-06-02 00:48:35Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Widget_LoginOrSignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->setNoRender();
      return;
    }
    
    // Display form
    $form = $this->view->form = new User_Form_Login();;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }
  }
  
  public function getCacheKey()
  {
    return false;
  }
}