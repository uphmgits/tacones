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
    $this
      ->setTitle('Inspection Settings')
      ->setDescription('Set inspection.');
    
    $this->addElement('Text', 'marketplace_inspection', array(
		    'label' => 'Cost',
		    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspection', 0),
		    'required' => true,
        'validators' => array(
          'float',
        )
		));
    $this->addElement('Checkbox', 'marketplace_inspection_enable', array(
		    'label' => 'Is inspection enabled',
        'description' => 'Enabled',
		    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.inspectionenable', 1),
		));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
