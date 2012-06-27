<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_Form_Admin_Inspection extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Shipping + Handling Settings');
    
    $this->addElement('Text', 'marketplace_inspection', array(
		    'label' => 'Shipping + Handling (%)',
		    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspection', 0),
		    'required' => true,
        'validators' => array(
          'float',
        )
		));
    $this->addElement('Text', 'marketplace_inspection_vip', array(
		    'label' => 'VIP Shipping + Handling (%)',
		    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspection_vip', 0),
		    'required' => true,
        'validators' => array(
          'float',
        )
		));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
