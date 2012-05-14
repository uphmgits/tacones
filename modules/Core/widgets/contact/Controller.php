<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 9382 2011-10-14 00:41:45Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Widget_ContactController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->form = $form = new Core_Form_Contact();
    $form->setAction(Zend_Controller_Front::getInstance()
      ->getRouter()->assemble(array(
        'module' => 'core',
        'controller' => 'help',
        'action' => 'contact',
      ), 'default', true))
      ;
  }
}