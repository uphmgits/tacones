<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Viglink.php 9411 2011-10-19 21:29:06Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Settings_Viglink extends Engine_Form
{
  public function init()
  {
    $defaultId = md5(Engine_Api::_()->getDbtable('settings', 'core')->getSetting('core.license.key'));
    $subid = Engine_Api::_()->getDbtable('settings', 'core')->getSetting('core.viglink.subid', $defaultId);
    
    // Set form attributes
    $this->setTitle('VigLink');

    $description = $this->getTranslator()->translate(
        'Simply click enable below to start using ' . 
        '<a href="%1$s" target="_blank">VigLink</a>. You can claim or visit ' . 
        'your account <a href="%3$s" target="_blank">here</a> on our ' . 
        'site. If you already have a VigLink account, you can enter in your ' .
        'API key as found <a href="%2$s" target="_blank">here</a>. ' . 
        'If you already have an account, please empty the Sub ID field below.' .
        '<a class="admin help" href="%4$s" target="_blank"> </a>');
    $description = vsprintf($description, array(
      'http://www.viglink.com/?vgref=33113',
      'http://www.viglink.com/account?vgref=33113',
      'http://www.socialengine.net/account/viglink?id=' . urlencode($subid),
      'http://www.socialengine.net/support/article?q=206&question=Viglink-Setup'
    ));
    $this->setDescription($description);
    
    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
    
    // element: enabled
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Enable?',
      'multiOptions' => array(
        '1' => 'Yes, enable VigLink on my site.',
        '0' => 'No, VigLink is disabled.',
      ),
      'value' => 0,
    ));
    
    // Element: code
    $this->addElement('Text', 'code', array(
      'label' => 'API Key',
      'filters' => array(
        'StringTrim',
      ),
      'value' => 'fc79953012d3b0f534531172059792a1',
    ));
    
    // element: subid
    $this->addElement('Text', 'subid', array(
      'label' => 'Sub ID',
      'description' => 'This is your VigLink sub ID, which is based on your SE license ' . 
          'key. Please make sure your license key is valid on the admin ' . 
          'dashboard in order to be able to claim your account.',
      'value' => $subid,
    ));
    
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}