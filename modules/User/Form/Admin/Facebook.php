<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Facebook.php 9041 2011-06-30 04:37:55Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Facebook extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Facebook Integration')
      ->setDescription('USER_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod("POST");
      ;

    $description = $this->getTranslator()->translate('USER_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION');
    $description = vsprintf($description, array(
      'http://www.facebook.com/developers/apps.php',
    ));
    $this->setDescription($description);


    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'key', array(
      'label' => 'Facebook API Key',
      'description' => '',
    ));
    
    $this->addElement('Text', 'secret', array(
      'label' => 'Facebook App Secret',
      'description' => 'This is a 36 character string of letters and numbers provided by Facebook when you create an Application in your account.',
    ));

    $this->addElement('Text', 'appid', array(
      'label' => 'Facebook App ID',
      'description' => '',
    ));

    $this->addElement('Radio', 'enable', array(
      'label' => 'Integrate Features',
      'description' => 'What features would you like to integrate?',
      'multiOptions' => array(
        'none'  => 'None',
        'login' => 'Login only',
        'publish' => 'Publish to Facebook',
      ),
      'value' => 'none'
    ));


    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));

  }
}
